<?php

namespace Zqhong\FastdEloquent\Test;

use FastD\Container\Container;

class Runtime
{
    /**
     * @var Container
     */
    public static Container $container;

    public function __construct()
    {
        static::$container = new Container();
    }
}
