<?php

namespace Zqhong\FastdEloquent\Test;

use FastD\Config\Config;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Zqhong\FastdEloquent\EloquentService;

class TestCase extends BaseTestCase
{
    protected function setUp()
    {
        new Runtime();
        $config = new Config();
        $config->merge([
            'database' => [
                'default' => [
                    'adapter' => 'sqlite',
                    'name' => ':memory:',
                    'prefix' => '',
                ],
            ],
        ]);
        container()->add('config', $config);

        $provider = new EloquentService();
        $provider->register(Runtime::$container);

        // 创建一张 Post 表用于测试
        Manager::schema()->dropIfExists('posts');
        Manager::schema()->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('author', 255);
            $table->string('title', 255);
            $table->text('content');
            $table->integer('created_at', false, true);
            $table->integer('updated_at', false, true);
        });
    }

    protected function tearDown()
    {
        Manager::schema()->dropIfExists('posts');
        Manager::connection()->disconnect();
    }
}
