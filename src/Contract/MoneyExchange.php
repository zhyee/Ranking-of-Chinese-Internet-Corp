<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/1
 * Time: 19:26
 */

namespace Rrclic\Contract;

interface MoneyExchange
{
    // 人民币
    const MONEY_RMB = 1;

    // 美元
    const MONEY_USD = 2;

    // 港元
    const MONEY_HKD = 3;

    // 支持的币种
    const SUPPORT_CURRENCIES = [
        self::MONEY_RMB,
        self::MONEY_USD,
        self::MONEY_HKD,
    ];

    // 币种单位
    const CURRENCY_NAMES = [
        self::MONEY_RMB => '元',
        self::MONEY_USD => '美元',
        self::MONEY_HKD => '港元',
    ];


    /**
     * 从$from币种转换$value金额的货币到$to币种
     * @param $from
     * @param $to
     * @param $value
     * @return string
     */
    public function exchange($from, $to, $value);
}