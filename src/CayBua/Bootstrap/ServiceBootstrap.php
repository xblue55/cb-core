<?php

namespace CayBua\Bootstrap;

use CayBua\Auth\UsernameAccountType;
use CayBua\Auth\Manager;
use CayBua\User\Service;
use CayBua\BootstrapInterface;
use CayBua\Constants\Services;
use CayBua\Fractal\CustomSerializer;

use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Db\Adapter\Pdo\Mysql;

use PhalconApi\Auth\TokenParsers\JWTTokenParser;
use PhalconRest\Api;

use League\Fractal\Manager as FractalManager;

class ServiceBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        /**
         * @description Config - \Phalcon\Config
         */
        $di->setShared(Services::CONFIG, $config);

        /**
         * @description Phalcon - \Phalcon\Db\Adapter\Pdo\Mysql
         */
        $di->set(Services::DB, function () use ($config, $di) {

            $config = $config->get('database')->toArray();
            $adapter = $config['adapter'];
            unset($config['adapter']);
            $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

            /** @var Mysql $connection */
            $connection = new $class($config);

            // Assign the eventsManager to the db adapter instance
            $connection->setEventsManager($di->get(Services::EVENTS_MANAGER));

            return $connection;
        });

        /**
         * @description Phalcon - \Phalcon\Mvc\Url
         */
        $di->set(Services::URL, function () use ($config) {

            $url = new UrlResolver;
            $url->setBaseUri($config->get('application')->baseUri);
            return $url;
        });

        /**
         * @description Phalcon - \Phalcon\Mvc\View\Simple
         */
        $di->set(Services::VIEW, function () use ($config) {

            $view = new View;
            $view->setViewsDir($config->get('application')->viewsDir);

            return $view;
        });

        /**
         * @description Phalcon - EventsManager
         */
        $di->setShared(Services::EVENTS_MANAGER, function () use ($di, $config) {

            return new EventsManager;
        });

        /**
         * @description Phalcon - TokenParsers
         */
        $di->setShared(Services::TOKEN_PARSER, function () use ($di, $config) {

            return new JWTTokenParser($config->get('authentication')->secret, JWTTokenParser::ALGORITHM_HS256);
        });


        /**
         * @description PhalconRes - \Phalcon\Logger
         */
        $di->setShared(Services::LOGGER, function () use ($config) {
            $logger = new FileAdapter($config->get('application')->loggerUrl);
            return $logger;
        });

        /**
         * @description Phalcon - \Phalcon\Mvc\Model\Manager
         */
        $di->setShared(Services::MODELS_MANAGER, function () use ($di) {

            $modelsManager = new ModelsManager;
            return $modelsManager->setEventsManager($di->get(Services::EVENTS_MANAGER));
        });

        /**
         * @description PhalconRest - \League\Fractal\Manager
         */
        $di->setShared(Services::FRACTAL_MANAGER, function () {

            $fractal = new FractalManager;
            $fractal->setSerializer(new CustomSerializer);

            return $fractal;
        });

        /**
         * @description PhalconRest - \PhalconRest\User\Service
         */
        $di->setShared(Services::USER_SERVICE, new Service());

        /**
         * @description Phalcon - AuthManager
         */
        $di->setShared(Services::AUTH_MANAGER, function () use ($di, $config) {

            $authManager = new Manager($config->get('authentication')->expirationTime);
            $authManager->registerAccountType(UsernameAccountType::NAME, new UsernameAccountType);

            return $authManager;
        });

    }
}