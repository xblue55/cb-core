<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/13/17
 * Time: 09:51
 */

namespace CayBua\Bootstrap;

use CayBua\Api;
use CayBua\BootstrapInterface;

use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Simple as SimpleView;

class RouteBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api->get('/' . $config->get('domainName') . '/documentation.html',
            function () use ($api, $config) {
                /** @var \Phalcon\Mvc\View\Simple $view */
                $view = new SimpleView;
                $view->setViewsDir(__DIR__ . '/../Views/');
                $view->setVar('title', $config->get('application')->title);
                $view->setVar('description', $config->get('application')->description);
                $view->setVar(
                    'documentationPath',
                    $config->get('hostName') .
                    '/' . $config->get('domainName') .
                    '/export/documentation.json'
                );
                return $view->render('general/documentation');
            });
    }
}