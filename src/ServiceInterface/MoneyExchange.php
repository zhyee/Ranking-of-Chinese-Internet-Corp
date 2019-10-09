<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/1
 * Time: 19:26
 */

namespace Rrclic\ServiceInterface;

interface MoneyExchange
{
    /**
     * 从$from币种转换$value金额的货币到$to币种
     * @param $from
     * @param $to
     * @param $value
     * @return string
     */
    public function exchange($from, $to, $value);
}