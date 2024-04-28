<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 18:10
 */

namespace Rrclic\Service;

use QL\QueryList;
use Rrclic\Library\BaseClass;
use Rrclic\Contract\MoneyExchange;

class MoneyExchangeFromSina extends BaseClass implements MoneyExchange
{
    // 查询汇率url
    const SINA_EXCHANGE_RATE_API = 'https://hq.sinajs.cn/rn={timestamp}list=fx_s{from}{to}';

    /**
     * https://w.sinajs.cn/?_=0.5115431930786267&list=fx_susdcny
     */
    const SINA_EXCHANGE_RATE_API_V2 = 'https://w.sinajs.cn/?_=%f&list=fx_s%s%s';

    const CURRENCY_EN_NAMES = [
        MoneyExchange::MONEY_RMB => 'cny',
        MoneyExchange::MONEY_USD => 'usd',
        MoneyExchange::MONEY_HKD => 'hkd',
    ];


    /**
     * 查询当前汇率，返回1元的$from币种能够兑换多少元的$to币种
     * @param $from
     * @param $to
     * @return string
     * @throws \HttpRuntimeException
     */
    public function getExchangeRate($from, $to): string
    {
        if (!in_array($from, MoneyExchange::SUPPORT_CURRENCIES) || !in_array($to, MoneyExchange::SUPPORT_CURRENCIES)) {
            throw new \InvalidArgumentException('bad arguments!');
        }
        static $res = [];
        $exchange = $from . '->' . $to;
        if (isset($res[$exchange])) {
            return $res[$exchange];
        }

        $url = sprintf(self::SINA_EXCHANGE_RATE_API_V2, microtime(true) / 10000000000, self::CURRENCY_EN_NAMES[$from], self::CURRENCY_EN_NAMES[$to]);

        $ch = curl_init();

        curl_setopt_array($ch, [
           CURLOPT_URL => $url,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_TIMEOUT => 30,
           CURLOPT_HEADER => false,
           CURLOPT_HTTPHEADER => [
               'Referer: https://gu.sina.cn/fx/hq/quotes.php?vt=4&wm=4007mpampampampampampampltbr&code=USDCNY&autocallup=no&isfromsina=no'
           ],
        ]);

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \HttpRuntimeException("unable to query currency exchange rate: " . curl_error($ch));
        }

        if (floor(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) / 100) != 2) {
            throw new \HttpRuntimeException("query currency exchange rate return abnormal http code: " . curl_getinfo($ch, CURLINFO_RESPONSE_CODE));
        }

        if (!$html) {
            throw new \HttpRuntimeException("query currency exchange rate return empty response");
        }

        $arr = explode(',', $html);
        return $res[$exchange] = $arr[1];
    }

    /**
     * 从$from币种转换$value金额的货币到$to币种
     * @param $from
     * @param $to
     * @param $value
     * @return string
     */
    public function exchange($from, $to, $value): string
    {
        $rate = 1;
        if ($from != $to) {
            try {
                $rate = $this->getExchangeRate($from, $to);
            } catch (\Exception $e) {
                echo "查询汇率失败：" . $e->getMessage() . PHP_EOL;
            }
        }
        return bcmul($value, $rate, 2);
    }
}