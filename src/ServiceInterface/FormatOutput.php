<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/23
 * Time: 10:01
 */

namespace Rrclic\ServiceInterface;

interface FormatOutput
{
    public function formatOut(array $data, $file = null);
}