<?php

use Zqhong\FastdEloquent\Test\Application;

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('container')) {
    function container(): \FastD\Container\Container
    {
        return \Zqhong\FastdEloquent\Test\Runtime::$container;
    }
}
