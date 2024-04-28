<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 16:46
 */

namespace Rrclic\Service;

use Rrclic\Entity\CurlTask;
use Rrclic\Library\BaseClass;
use Rrclic\Contract\MarketValue;
use Rrclic\Contract\MoneyExchange;
use Rrclic\Library\ConcurrentCurl;

class MarketValueFromEastMoney extends BaseClass implements MarketValue
{
    const MARKET_VALUE_URL = 'http://push2.eastmoney.com/api/qt/stock/get?secid={{param1}}&fields=f58,f116,f172';
    const MARKET_VALUE_API_V2 = 'http://push2.eastmoney.com/api/qt/stock/get?secid=%s&fields=f58,f116,f172';

    /**
     * deprecated
     */
    const QUOTE_CODE_URL = 'http://searchapi.eastmoney.com/api/suggest/get?input={{param1}}&type=14&token=D43BF722C8E33BDC906FB84D85E326E8&markettype=&mktnum=&jys=&classify=&securitytype=&count=5&_=1714306544640';

    const QUOTE_CODE_API_V2 = 'https://search-codetable.eastmoney.com/codetable/search/web?client=web&clientType=webSuggest&clientVersion=lastest&keyword=%s&pageIndex=1&pageSize=5&securityFilter=&_=%d';

    private array $marketValues = [];

    const MARKET_NAMES = [
        '美股',
        '港股',
        '深A',
        '沪A',
        '科创板',
    ];

    const MONEY_UNIT_MAP = [
        'USD' => MoneyExchange::MONEY_USD,
        'HKD' => MoneyExchange::MONEY_HKD,
        'RMB' => MoneyExchange::MONEY_RMB
    ];


    public function getSortedMarketValues($corpNameLists, $toMoneyUnit = MoneyExchange::MONEY_USD): array
    {
        $cc = (new ConcurrentCurl())->setMaxConcurrency(16);

        foreach ($corpNameLists as $corpName) {
            $ch = curl_init();
            $url = sprintf(self::QUOTE_CODE_API_V2, urlencode($corpName), floor(microtime(true) * 1000));
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Language: zh-CN,zh;q=0.9',
                    'Connection: keep-alive',
                    'Cache-Control: max-age=0',
                    'Cookie: qgqp_b_id=41cbf4607be7c6b3e4c71fa5bd588132; st_si=98128569767515; st_pvi=54876823624769; st_sp=2024-04-28%2018%3A45%3A22; st_inirUrl=https%3A%2F%2Fwww.eastmoney.com%2F; st_sn=4; st_psi=20240428201544875-111000300841-5325275290; st_asi=20240428201544875-111000300841-5325275290-Web_so_srk-1',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: none',
                    'Sec-Fetch-User: ?1',
                    'Upgrade-Insecure-Requests: 1',
                    'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                    'sec-ch-ua: "Google Chrome";v="123", "Not:A-Brand";v="8", "Chromium";v="123"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                ]
            ]);
            $task = (new CurlTask())
                ->setId($corpName)
                ->setCurlHandler($ch)
                ->setMaxRetryCount(5)
                ->setSuccessCall(function (CurlTask $task) use ($corpName, $toMoneyUnit, $cc) {
                    $body = curl_multi_getcontent($task->getCurlHandler());
                    $quoteID = '';
                    if ($body) {
                        $body = json_decode($body, true);
                        if ($body && is_array($body['result']) && count($body['result']) > 0) {
                            foreach ($body['result'] as $data) {
                                if (in_array($data['securityTypeName'], self::MARKET_NAMES) && $data['market'] != "" && $data['code'] != "") {
                                    $quoteID = $data['market'] . '.' . $data['code'];
                                    break;
                                }
                            }
                        }
                    }
                    if (!$quoteID) {
                        echo $corpName . '>>>>>>>查询QuoteID失败，可能已退市' . PHP_EOL;
                        return;
                    }

                    $ch = curl_init();
                    $url = str_replace('{{param1}}', $quoteID, self::MARKET_VALUE_URL);
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HEADER => false,
                        CURLOPT_TIMEOUT => 8,
                    ]);

                    $task = (new CurlTask())
                        ->setId($corpName)
                        ->setCurlHandler($ch)
                        ->setMaxRetryCount(5)
                        ->setSuccessCall(function (CurlTask $task) use ($corpName, $toMoneyUnit) {
                            $body = curl_multi_getcontent($task->getCurlHandler());

                            if ($body) {
                                $body = json_decode($body, true);
                                if (isset($body['data']['f116']) && isset($body['data']['f172'])) {
                                    $market = $body['data']['f116'];
                                    $valueUnit = $body['data']['f172'];
                                    $fromMoneyUnit = self::MONEY_UNIT_MAP[$valueUnit] ?? MoneyExchange::MONEY_RMB;
                                    $this->marketValues[$corpName][MoneyExchange::MONEY_RMB] = MoneyExchangeFromSina::getInstance()->exchange($fromMoneyUnit, MoneyExchange::MONEY_RMB, $market);
                                    $this->marketValues[$corpName][MoneyExchange::MONEY_USD] = MoneyExchangeFromSina::getInstance()->exchange($fromMoneyUnit, MoneyExchange::MONEY_USD, $market);
                                    $this->marketValues[$corpName][MoneyExchange::MONEY_HKD] = MoneyExchangeFromSina::getInstance()->exchange($fromMoneyUnit, MoneyExchange::MONEY_HKD, $market);
                                    return;
                                }
                            }
                            echo $corpName . '>>>>>查询市值失败' . PHP_EOL;
                        })->setErrorCall(function (CurlTask $task, $errMsg) {
                            echo $errMsg . PHP_EOL;
                        });
                    $cc->appendTask($task);
                })
                ->setErrorCall(function (CurlTask $task, $errMsg) {
                    echo $errMsg . PHP_EOL;
                });

            $cc->appendTask($task);
        }
        $cc->bootstrap();
        arsort($this->marketValues);
        return $this->marketValues;
    }
}