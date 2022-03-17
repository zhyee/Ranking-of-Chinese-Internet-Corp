<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/23
 * Time: 10:01
 */

namespace Rrclic\Contract;

interface FormatOutput
{
    public function formatOut(array $data, $unit = MoneyExchange::MONEY_USD, $file = null);
}