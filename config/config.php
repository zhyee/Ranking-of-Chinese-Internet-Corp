<?php
/**
 * app 基础配置
 */

use Monolog\Logger;
use Rrclic\Service\MarketValueFromEastMoney;

return [
    'displayErrorDetails' => true, // Should be set to false in production
    'logger' => [
        'name' => 'rrclic',
        'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
        'level' => Logger::DEBUG,
    ],
    'companies' => [
        '腾讯控股' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=116.00700&fields=f58,f116,f172'
        ],
        '腾讯音乐' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=106.TME&fields=f58,f116,f172'
        ],
        '阿里巴巴' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=106.BABA&fields=f58,f116,f172'
        ],
        '美团点评' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=116.03690&fields=f58,f116,f172'
        ],
        '京东' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=105.JD&fields=f58,f116,f172'
        ],
        '百度' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=105.BIDU&fields=f58,f116,f172'
        ],
        '网易' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=105.NTES&fields=f58,f116,f172'
        ],
        '网易有道' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=106.DAO&fields=f58,f116,f172'
        ],
        '阅文集团' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=116.00772&fields=f58,f116,f172'
        ],
        '新浪' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=105.SINA&fields=f58,f116,f172'
        ],
        '微博' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=105.WB&fields=f58,f116,f172'
        ],
        '搜狐' => [
            MarketValueFromEastMoney::class => 'http://push2.eastmoney.com/api/qt/stock/get?secid=105.SOHU&fields=f58,f116,f172'
        ],
        '搜狗',
        '哔哩哔哩',
        '拼多多',
        '携程',
        '同程艺龙',
        '科大讯飞',
        '途牛',
        '三六零',
        '爱奇艺',
        '58同城',
        '陌陌',
        '斗鱼',
        '虎牙',
        '唯品会',
        '聚美优品',
        '蘑菇街',
        '用友网络',
        '微盟集团',
        '二三四五',
        '世纪华通',
        '第九城市',
        '完美世界',
        '前程无忧',
        '巨人网络',
        '苏宁易购',
        '猎豹移动',
        '汽车之家',
        '平安好医生',
        '东软集团',
        '昆仑万维',
        '映客',
        '顺网科技',
        '美图公司',
        '搜房网',
        '泛微网络',
        '浪潮信息',
        '浪潮软件',
        '浪潮国际',
        '二六三',
        '拉卡拉',
        '拍拍贷',
        '宜人贷',
        '趣店',
        '人人网',
        '金山软件',
        '新华网',
        '人民网',
        '金蝶国际',
        '游族网络',
        '恺英网络',
        '东方财富',
        '大智慧',
        '老虎证券',
        '同花顺',
        '趣头条',
        '触宝科技',
        '万达信息',
        '齐屹科技',
        '国双',
        '迅雷',
        '暴风集团',
        '富途控股',
        '欢聚时代',
        '畅游',
        '易车',
        '跟谁学',
        '乐居',
        '极光',
        '网达软件',
        '启明信息',
        '博彦科技',
        '天源迪科',
        '深信服',
        '科大国创',
        '和信贷',
        '好未来',
        '流利说',
        '360金融',
        '鲁大师',
        '金山办公',
    ],
];
