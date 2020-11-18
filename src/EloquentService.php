<?php

namespace Zqhong\FastdEloquent;

use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Zqhong\FastdEloquent\Database\ConnectionFactory;

/**
 * Class EloquentService
 *
 * @package ServiceProvider
 */
class EloquentService implements ServiceProviderInterface
{
    /**
     * @var Manager
     */
    protected Manager $capsule;

    /**
     * 注册 Eloquent 服务
     *
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $this->capsule = new Manager();

        $dbConfig = $container->get('config')->get('database', []);
        foreach ($dbConfig as $dbName => $config) {
            $this->addConnection($dbName, $config);
        }

        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
        $container->add('eloquent_db', $this->capsule);

        $this->setPageResolver($container);
        $this->setEvent($container);
        $this->extendManager();
    }

    /**
     * 扩展
     */
    protected function extendManager()
    {
        $connFactory = new ConnectionFactory($this->capsule->getContainer());
        $drivers = ['mysql', 'sqlite'];

        foreach ($drivers as $driver) {
            $this
                ->capsule
                ->getDatabaseManager()
                ->extend($driver, function (array $config, $name) use ($connFactory) {
                    return $connFactory->make($config, $name);
                });
        }
    }

    /**
     * event dispatcher 设置
     *
     * @param Container $container
     */
    protected function setEvent(Container $container)
    {
        $eventDispatcher = new Dispatcher();
        $this->capsule->setEventDispatcher($eventDispatcher);
        $container->add('eloquent_event_dispatcher', $eventDispatcher);
    }

    /**
     * 分页设置
     *
     * @param Container $container
     */
    protected function setPageResolver(Container $container)
    {
        LengthAwarePaginator::currentPageResolver(function ($pageName) use ($container) {
            return (int)Arr::get(array_merge($_GET, $_POST), $pageName, 1);
        });
    }

    protected function addConnection($dbName, $dbConfig)
    {
        $setting = [
            'driver' => Arr::get($dbConfig, 'adapter', 'mysql'),
            'host' => Arr::get($dbConfig, 'host', '127.0.0.1'),
            'port' => Arr::get($dbConfig, 'port', 3306),
            'database' => Arr::get($dbConfig, 'name'),
            'username' => Arr::get($dbConfig, 'user'),
            'password' => Arr::get($dbConfig, 'pass'),
            'charset' => Arr::get($dbConfig, 'charset', 'utf8'),
            'collation' => Arr::get($dbConfig, 'collation', 'utf8_general_ci'),
            'prefix' => Arr::get($dbConfig, 'prefix', ''),
            // PDO options
            'options' => Arr::get($dbConfig, 'options', []),
        ];

        if ($setting['driver'] == 'mysql') {
            $setting['timezone'] = Arr::get($dbConfig, 'timezone');
            $setting['modes'] = Arr::get($dbConfig, 'modes');
            $setting['strict'] = Arr::get($dbConfig, 'strict');
        }

        $this->capsule->addConnection($setting, $dbName);
    }
}
