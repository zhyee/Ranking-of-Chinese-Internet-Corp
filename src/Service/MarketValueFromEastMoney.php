<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 16:46
 */

namespace Rrclic\Service;

use Rrclic\Library\BaseClass;
use Rrclic\Contract\MarketValue;
use Rrclic\Contract\MoneyExchange;

class MarketValueFromEastMoney extends BaseClass implements MarketValue
{
    public function getSortedMarketValues($corpNameLists, $unit = MoneyExchange::MONEY_USD)
    {
        // TODO: Implement getTotalMarketCap() method.
    }
}