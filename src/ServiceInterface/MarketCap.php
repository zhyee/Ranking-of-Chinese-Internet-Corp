<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 15:02
 */

namespace Rrclic\ServiceInterface;
use Rrclic\Service\MoneyExchangeFromSina;

interface MarketCap
{
    public function getTotalMarketCap($corpNameLists, $unit = MoneyExchangeFromSina::MONEY_USD);

    public function getSortedMarketValues();
}