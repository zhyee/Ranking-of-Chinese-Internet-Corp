<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/23
 * Time: 20:28
 */

namespace Rrclic\Service;

use Rrclic\Library\Helper;
use Rrclic\Contract\FormatOutput;

class HtmlFormatOutput implements FormatOutput
{
    public function formatOut(array $data, $file = null)
    {
        ob_start();
        echo <<<EOT
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>the Realtime Ranking of Chinese Listed Internet Corporation.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
    <table class="table table-hover table-bordered">
    <thead>
    <tr><th class="text-center">排名</th><th class="text-center">公司</th><th class="text-center">市值（单位：亿美元）</th></tr>
</thead>
<tbody>
EOT;

        $i = 0;
        foreach ($data as $name => $value)
        {
            ++$i;
            echo "<tr><td>{$i}</td><td>{$name}</td><td>" . bcdiv($value, 100000000, 2) . "</td></tr>\n";
        }

        echo <<<EOT
</tbody>
    
</table>
</div>
</body>
</html>
EOT;

        $output = ob_get_clean();

        if ($file) {
            Helper::writeToFile($file, $output);
        } else {
            echo $output;
        }

    }
}