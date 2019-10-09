<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 16:46
 */

namespace Rrclic\Service;

use Rrclic\Library\BaseClass;
use Rrclic\ServiceInterface\MarketCap;

class MarketCapFromEastMoney extends BaseClass implements MarketCap
{
    public function getTotalMarketCap($corpNameLists, $unit = MoneyExchangeFromSina::MONEY_USD)
    {
        // TODO: Implement getTotalMarketCap() method.
    }

    public function getSortedMarketValues()
    {
        // TODO: Implement getSortedMarketValues() method.
    }
}