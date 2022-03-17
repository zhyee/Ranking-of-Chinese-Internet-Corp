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

    const QUOTE_CODE_URL = 'http://searchapi.eastmoney.com/api/suggest/get?input={{param1}}&type=14&token=D43BF722C8E33BDC906FB84D85E326E8&markettype=&mktnum=&jys=&classify=&securitytype=&count=5&_=1583570579156';

    private $marketValues = [];

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


    public function getSortedMarketValues($corpNameLists, $toMoneyUnit = MoneyExchange::MONEY_USD)
    {
        $cc = (new ConcurrentCurl())->setMaxConcurrency(16);

        foreach ($corpNameLists as $corpName) {
            $ch = curl_init();
            $url = str_replace('{{param1}}', $corpName, self::QUOTE_CODE_URL);
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_TIMEOUT => 8,
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
                        if ($body && $body['QuotationCodeTable']['Data']) {
                            foreach ($body['QuotationCodeTable']['Data'] as $data) {
                                if (in_array($data['SecurityTypeName'], self::MARKET_NAMES)) {
                                    $quoteID = $data['QuoteID'];
                                    break;
                                }
                            }
                        }
                    }
                    if (!$quoteID) {
                        echo $corpName . '>>>>>>>查询QuoteID失败' . PHP_EOL;
                        return;
                    }

                    $ch = curl_init();
                    $url = str_replace('{{param1}}', $quoteID, self::MARKET_VALUE_URL);
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HEADER => false,
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
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
                                $market = $body['data']['f116'];
                                $valueUnit = $body['data']['f172'];
                                $fromMoneyUnit = isset(self::MONEY_UNIT_MAP[$valueUnit]) ? self::MONEY_UNIT_MAP[$valueUnit] : MoneyExchange::MONEY_RMB;
                                $this->marketValues[$corpName][MoneyExchange::MONEY_RMB] = MoneyExchangeFromSina::getInstance()->exchange($fromMoneyUnit, MoneyExchange::MONEY_RMB, $market);
                                $this->marketValues[$corpName][MoneyExchange::MONEY_USD] = MoneyExchangeFromSina::getInstance()->exchange($fromMoneyUnit, MoneyExchange::MONEY_USD, $market);
                                $this->marketValues[$corpName][MoneyExchange::MONEY_HKD] = MoneyExchangeFromSina::getInstance()->exchange($fromMoneyUnit, MoneyExchange::MONEY_HKD, $market);
                                return;
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