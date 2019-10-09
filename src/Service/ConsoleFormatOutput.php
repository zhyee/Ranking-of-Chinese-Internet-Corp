<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/23
 * Time: 20:14
 */

namespace Rrclic\Service;

use Rrclic\Library\Helper;
use Rrclic\ServiceInterface\FormatOutput;

class ConsoleFormatOutput implements FormatOutput
{
    public function formatOut(array $data, $file = null)
    {
        ob_start();
        $i = 0;
        foreach ($data as $name => $value) {
            echo (++$i) . '. ' . $name . "：" . bcdiv($value, 100000000, 2) . '亿美元' . PHP_EOL;
        }
        $output = ob_get_clean();

        if ($file) {
            Helper::writeToFile($file, $output);
        } else {
            echo $output;
        }
    }
}