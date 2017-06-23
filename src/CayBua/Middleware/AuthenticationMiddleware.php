<?php

namespace CayBua\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconApi\Mvc\Plugin;
use CayBua\Constants\Services;

class AuthenticationMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute()
    {
        $token = $this->request->getToken();
        if ($token) {
            $this->authManager->authenticateToken($token);
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}