<?php

use Illuminate\Database\Connection;
use Illuminate\Events\Dispatcher;

if (!function_exists('eloquent_db')) {
    /**
     * 获取数据库连接实例
     *
     * @param string $name
     * @return Connection
     */
    function eloquent_db($name = 'default'): Connection
    {
        return container()->get('eloquent_db')->getConnection($name);
    }
}

if (!function_exists('eloquent_event_dispatcher')) {
    /**
     * @return Dispatcher
     */
    function eloquent_event_dispatcher(): object
    {
        return container()->get('eloquent_event_dispatcher');
    }
}
