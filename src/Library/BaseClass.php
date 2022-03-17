<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 15:15
 */

namespace Rrclic\Library;

abstract class BaseClass
{
    private static $instance;

    public static function getInstance(...$args)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static(...$args);
        }

        return self::$instance;
    }
}