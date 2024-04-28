<?php

namespace Rrclic\Service;

use PHPUnit\Framework\TestCase;

class MoneyExchangeFromSinaTest extends TestCase
{

    public function testExchangeFromSina() {

        $url = sprintf(MoneyExchangeFromSina::SINA_EXCHANGE_RATE_API_V2, microtime(true) / 10000000000, 'usd', 'cny');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Referer: https://gu.sina.cn/fx/hq/quotes.php?vt=4&wm=4007mpampampampampampampltbr&code=USDCNY&autocallup=no&isfromsina=no',
        ]);

        $resp = curl_exec($ch);
        echo $resp . "\n";

        $arr = explode(',', $resp);
        echo $arr[1] . "\n";

        $this->assertEquals(2, floor(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) / 100));

    }

}