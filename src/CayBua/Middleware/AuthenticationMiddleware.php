<?php

namespace CayBua\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconApi\Mvc\Plugin;

class AuthenticationMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute()
    {
        $request = $this->di->get(Services::REQUEST);
        $config = $this->di->get(Services::CONFIG);
        $accesstrustedkey = $request->getHeader('AccessTrustedKey');
        if (!empty($accesstrustedkey) && $accesstrustedkey == $config->get('authentication')->accesstrustedkey) {
            //Allow for server request
        } else {
            $token = $this->request->getToken();
            if ($token) {
                $this->authManager->authenticateToken($token);
            }
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}