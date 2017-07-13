<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/13/17
 * Time: 09:51
 */

namespace CayBua\Bootstrap;

use CayBua\BootstrapInterface;
use CayBua\Constants\Services;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

class RouteBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api->get('/documentation.html', function () use ($api, $config) {
            /** @var \Phalcon\Mvc\View\Simple $view */
            $view = $api->di->get(Services::VIEW);
            $view->setVar('title', $config->get('application')->title);
            $view->setVar('description', $config->get('application')->description);
            $view->setVar(
                'documentationPath',
                $config->get('hostName') .
                '/' . $config->get('domainName') .
                '/export/documentation.json'
            );
            return $view->render('/general/documentation');
        });
    }
}