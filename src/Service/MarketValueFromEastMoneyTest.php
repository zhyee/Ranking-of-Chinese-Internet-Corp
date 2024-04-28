<?php

namespace Rrclic\Service;

use PHPUnit\Framework\TestCase;

class MarketValueFromEastMoneyTest extends TestCase
{

    public function testShouldReturnMarketValueFromEastMoney() {

        $url = sprintf(MarketValueFromEastMoney::QUOTE_CODE_API_V2, urlencode('阿里巴巴'), floor(microtime(true) * 1000));
        echo $url . "\n";

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language: zh-CN,zh;q=0.9',
                'Connection: keep-alive',
                'Cache-Control: max-age=0',
                'Cookie: qgqp_b_id=41cbf4607be7c6b3e4c71fa5bd588132; st_si=98128569767515; st_pvi=54876823624769; st_sp=2024-04-28%2018%3A45%3A22; st_inirUrl=https%3A%2F%2Fwww.eastmoney.com%2F; st_sn=4; st_psi=20240428201544875-111000300841-5325275290; st_asi=20240428201544875-111000300841-5325275290-Web_so_srk-1',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'sec-ch-ua: "Google Chrome";v="123", "Not:A-Brand";v="8", "Chromium";v="123"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
            ]
        ]);

        $resp = curl_exec($ch);
        echo $resp . "\n";

        $this->assertEquals(2, floor(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) / 100));

    }

}