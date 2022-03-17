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
    const url = 'https://hq.sinajs.cn/rn={timestamp}list=fx_s{from}{to}';

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
     */
    public function getExchangeRate($from, $to)
    {
        if (!in_array($from, MoneyExchange::SUPPORT_CURRENCIES) || !in_array($to, MoneyExchange::SUPPORT_CURRENCIES)) {
            throw new \InvalidArgumentException('bad arguments!');
        }
        static $res = [];
        $exchange = $from . '->' . $to;
        if (isset($res[$exchange])) {
            return $res[$exchange];
        }

        $timestamp = (int)(microtime(true) * 1000);

        $url = str_replace(['{timestamp}', '{from}', '{to}'], [$timestamp, self::CURRENCY_EN_NAMES[$from], self::CURRENCY_EN_NAMES[$to]], self::url);

        $ql = QueryList::getInstance()->get($url, [],   ['headers' => [
        'Referer' => 'https://finance.sina.com.cn/money/forex/hq/CNYHKD.shtml'
        ]]);

        // "Referer", "https://finance.sina.com.cn/money/forex/hq/CNYHKD.shtml"

        $html = $ql->getHtml();
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
    public function exchange($from, $to, $value)
    {
        if ($from == $to) {
            $rate = 1;
        } else {
            $rate = $this->getExchangeRate($from, $to);
        }
        return bcmul($value, $rate, 2);
    }
}