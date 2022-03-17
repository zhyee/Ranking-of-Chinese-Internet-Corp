<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/23
 * Time: 20:14
 */

namespace Rrclic\Service;

use Rrclic\Library\Helper;
use Rrclic\Contract\FormatOutput;
use Rrclic\Contract\MoneyExchange;

class ConsoleFormatOutput implements FormatOutput
{
    public function formatOut(array $data, $unit = MoneyExchange::MONEY_USD, $file = null)
    {
        ob_start();
        $i = 0;
        foreach ($data as $name => $value) {
            echo str_pad(++$i, 2, '0', STR_PAD_LEFT) . '. ' . $name . "\t\t" . bcdiv($value[MoneyExchange::MONEY_USD], 100000000, 2) . '亿' . MoneyExchange::CURRENCY_NAMES[MoneyExchange::MONEY_USD]
                .  "\t\t" . bcdiv($value[MoneyExchange::MONEY_RMB], 100000000, 2) . '亿' . MoneyExchange::CURRENCY_NAMES[MoneyExchange::MONEY_RMB]
                .  "\t\t" . bcdiv($value[MoneyExchange::MONEY_HKD], 100000000, 2) . '亿' . MoneyExchange::CURRENCY_NAMES[MoneyExchange::MONEY_HKD] . PHP_EOL;
        }
        $output = ob_get_clean();

        if ($file) {
            Helper::writeToFile($file, $output);
        } else {
            echo $output;
        }
    }
}