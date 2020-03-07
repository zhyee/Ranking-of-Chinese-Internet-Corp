<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 15:02
 */

namespace Rrclic\Contract;

interface MarketValue
{
    public function getSortedMarketValues($corpNameLists, $toMoneyUnit = MoneyExchange::MONEY_USD);
}