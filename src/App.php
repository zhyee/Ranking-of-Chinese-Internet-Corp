<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/1
 * Time: 22:07
 */

namespace Rrclic;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Rrclic\ServiceInterface\FormatOutput;
use Rrclic\ServiceInterface\MarketCap;

class App
{

    private $marketCap;

    private $formatOutput;

    private $logger;

    /**
     * @var ContainerInterface
     */
    public static $container;


    public static function setContainer(ContainerInterface $c)
    {
        self::$container = $c;
    }

    /**
     * App constructor.
     * @param MarketCap $marketCap
     * @param FormatOutput $formatOutput
     * @param LoggerInterface $logger
     * @throws \Exception
     */
    public function __construct(MarketCap $marketCap, FormatOutput $formatOutput, LoggerInterface $logger)
    {
        $this->marketCap = $marketCap;

        $this->formatOutput = $formatOutput;

        $this->logger = $logger;

    }

    public function run()
    {
        if (is_null(self::$container)) {
            throw new \RuntimeException('运行前必须设置App::container', 50001);
        }

        $companies = self::$container->get('config')['companies'];

        $this->marketCap->getTotalMarketCap($companies);
        $marketValues = $this->marketCap->getSortedMarketValues();
        $this->formatOutput->formatOut($marketValues);
    }
}