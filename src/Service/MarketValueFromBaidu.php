<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 15:03
 */

namespace Rrclic\Service;

use QL\QueryList;
use Rrclic\Entity\CurlTask;
use Rrclic\Library\BaseClass;
use Rrclic\Library\ConcurrentCurl;
use Rrclic\Contract\MarketValue;
use Rrclic\Contract\MoneyExchange;

class marketValueFromBaidu extends BaseClass implements MarketValue
{
    private $url = 'https://www.baidu.com/s?ie=utf-8&f=3&rsv_bp=1&rsv_idx=1&tn=baidu&wd={corpName}&rsv_pq=bd403b510028262e&rsv_t=c4a6N2yDeeqZvi6HLRBRSxB4EG2vnfkHbC6CS1hu%2F5I7Jr46T3LRWmLLijw&rqlang=cn&rsv_enter=0&rsv_dl=ih_0&rsv_sug3=1&rsv_sug1=1&rsv_sug7=001&rsp=0&rsv_sug9=es_1_1&inputT=1624&rsv_sug4=3137&rsv_sug=9';

    const CURRENCY_NAMES = [
        '美元' => MoneyExchange::MONEY_USD,
        '港元' => MoneyExchange::MONEY_HKD,
        '元' => MoneyExchange::MONEY_RMB,
    ];

    private $marketValues = [];

    public function __construct()
    {
        $this->testHttps();
    }

    /**
     * 测试https是否可以正常访问，不可访问则使用http
     */
    private function testHttps()
    {
        $url = str_replace('{corpName}', '百度市值', $this->url);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        $html = curl_exec($ch);
        curl_close($ch);
        if (strlen($html) < 1024) {
            $this->url = str_replace('https://', 'http://', $this->url);
        }
    }

    /**
     * @param $market
     * @return string
     */
    public function getMoneyUnit($market)
    {
        foreach (self::CURRENCY_NAMES as $name => $moneyUnit)
        {
            if (strpos($market, $name) !== false) {
                return $moneyUnit;
            }
        }
        return MoneyExchange::MONEY_USD;
    }

    public function getSortedMarketValues($corpNameLists, $moneyType = MoneyExchange::MONEY_USD)
    {
        if (is_string($corpNameLists)) {
            $corpNameLists = [$corpNameLists];
        }

        $searchMarketUrls = array_map(function ($corpName) {
            if (substr($corpName, -2) !== '市值') {
                $corpName .= '/市值';
            }
            return str_replace('{corpName}', $corpName, $this->url);
        }, $corpNameLists);

        $urlForCorps = array_combine($searchMarketUrls, $corpNameLists);

        $cc = (new ConcurrentCurl())->setMaxConcurrency(16);

        foreach ($urlForCorps as $url => $corpName) {
            $ch = curl_init();
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
                ->setSuccessCall(function (CurlTask $task) use ($corpName, $moneyType, $cc) {
                    $html = curl_multi_getcontent($task->getCurlHandler());
                    $ql = new QueryList();
                    $ql->setHtml($html);
                    $marketValue = $ql->find('#1')->find('.c-result-content:eq(0)')->find('.c-pc-font-24')->text();
                    if ($marketValue) {
                        $marketValue = strstr($marketValue, '元', true) . '元';
                        $this->marketValues[$corpName] = $this->format($marketValue, $moneyType);
                        return;
                    }

                    $divDom = $ql->find('div.op-sotckdynamic:eq(0)');
                    $unit = $divDom->find('.op-stockdynamic-moretab-cur-unit:eq(0)')->text();
                    $market = $divDom->find('.op-stockdynamic-moretab-info li:last .op-stockdynamic-moretab-info-value')->text();
                    if ($market && $unit) {
                        $this->marketValues[$corpName] = $this->format($market . $unit, $moneyType);
                        return;
                    }
                    echo "{$corpName}请求失败：寻找不到匹配的内容" . PHP_EOL;
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

    /**
     * @param $marketValue
     * @param int $unit
     * @return string
     */
    public function format($marketValue, $unit = MoneyExchange::MONEY_USD)
    {
        preg_match('/^[\d\.]+/', $marketValue, $num);
        $num = $num[0];

        $moneyUnit = $this->getMoneyUnit($marketValue);

        if (strpos($marketValue, '万') !== false) {
            $num = bcmul($num, 10000, 2);
        }

        if (strpos($marketValue, '亿') !== false) {
            $num = bcmul($num, 100000000, 2);
        }

        if ($moneyUnit != $unit)
        {
            $num = MoneyExchangeFromSina::getInstance()->exchange($moneyUnit, $unit, $num);
        }

        return $num;
    }
}
