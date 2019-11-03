<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 14:56
 */

use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;
use Rrclic\App;
use Rrclic\Service\ConsoleFormatOutput;
use Rrclic\Service\HtmlFormatOutput;
use Rrclic\Service\marketValueFromBaidu;
use Rrclic\Service\MoneyExchangeFromSina;
use Rrclic\Contract\FormatOutput;
use Rrclic\Contract\MarketValue;
use Rrclic\Contract\MoneyExchange;

require __DIR__ . '/../vendor/autoload.php';


$builder = new ContainerBuilder();

$builder->addDefinitions(
    [
        'config' => require __DIR__ . '/../config/config.php',
        'database' => require __DIR__ . '/../config/database.php',
        LoggerInterface::class => DI\factory(function (ContainerInterface $c) {
            $loggerSetting = $c->get('config')['logger'];
            $logger = new Logger($loggerSetting['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSetting['path'], $loggerSetting['level']);
            $logger->pushHandler($handler);
            return $logger;
        }),
        MarketValue::class => DI\create(marketValueFromBaidu::class),
        MoneyExchange::class => DI\create(MoneyExchangeFromSina::class),
    ]
);

if (PHP_SAPI === 'cli') {
    $builder->addDefinitions(
        [
            FormatOutput::class => DI\create(ConsoleFormatOutput::class),
        ]
    );
} else {
    $builder->addDefinitions(
        [
            FormatOutput::class => DI\autowire(HtmlFormatOutput::class),
        ]
    );
}

$container = $builder->build();

App::setContainer($container);
$app = $container->get(App::class);
$app->run();


