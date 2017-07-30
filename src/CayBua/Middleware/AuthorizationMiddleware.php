<?php

namespace CayBua\Middleware;

use CayBua\Api;
use CayBua\Constants\AclRoles;
use CayBua\Constants\Services;
use CayBua\Mvc\Plugin;

use Phalcon\Di;
use PhalconApi\Exception;
use PhalconApi\Constants\ErrorCodes;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

class AuthorizationMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute(Event $event, Api $api)
    {
        $collection = $api->getMatchedCollection();
        $endpoint = $api->getMatchedEndpoint();

        if (!$collection || !$endpoint) {
            return;
        }

        $allowed = $this->acl->isAllowed($this->userService->getRole(), $collection->getIdentifier(),
            $endpoint->getIdentifier());

        if($this->userService->getRole() == AclRoles::LOCAL_SERVICE){
            $config = $this->di->get(Services::CONFIG);
            if($config->get('accessTrustedKey') == ''){
                $allowed = true;
            }else{
                $allowed = false;
            }
        }

        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}