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
use Rrclic\ServiceInterface\MoneyExchange;

class MoneyExchangeFromSina extends BaseClass implements MoneyExchange
{
    // 人民币
    const MONEY_RMB = 'cny';

    // 美元
    const MONEY_USD = 'usd';

    // 港元
    const MONEY_HKD = 'hkd';

    // 查询汇率url
    const url = 'https://hq.sinajs.cn/rn={timestamp}list=fx_s{from}{to}';

    const SUPPORT_CURRENCIES = [
        self::MONEY_RMB,
        self::MONEY_USD,
        self::MONEY_HKD,
    ];


    /**
     * 查询当前汇率，返回1元的$from币种能够兑换多少元的$to币种
     * @param $from
     * @param $to
     * @return string
     */
    public function getExchangeRate($from, $to)
    {
        if (!in_array($from, self::SUPPORT_CURRENCIES) || !in_array($to, self::SUPPORT_CURRENCIES)) {
            throw new \InvalidArgumentException('bad arguments!');
        }
        static $res = [];
        $exchange = $from . $to;
        if (isset($res[$exchange])) {
            return $res[$exchange];
        }

        $timestamp = (int)(microtime(true) * 1000);

        $url = str_replace(['{timestamp}', '{from}', '{to}'], [$timestamp, $from, $to], self::url);

        $ql = QueryList::getInstance()->get($url);

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
        $rate = $this->getExchangeRate($from, $to);
        return bcmul($value, $rate, 2);
    }

    /**
     * 人民币转换美元
     * @param $value
     * @return string
     */
    public function rmb2usd($value)
    {
        return $this->exchange(self::MONEY_RMB, self::MONEY_USD, $value);
    }

    /**
     * 港币转换美元
     * @param $value
     * @return string
     */
    public function hkd2usd($value)
    {
        return $this->exchange(self::MONEY_HKD, self::MONEY_USD, $value);
    }

    /**
     * 美元转换人民币
     * @param $value
     * @return string
     */
    public function usd2rmb($value)
    {
        return $this->exchange(self::MONEY_USD, self::MONEY_RMB, $value);
    }

    /**
     * 港币兑换人民币
     * @param $value
     * @return string
     */
    public function hkd2rmb($value)
    {
        return $this->exchange(self::MONEY_HKD, self::MONEY_RMB, $value);
    }
}