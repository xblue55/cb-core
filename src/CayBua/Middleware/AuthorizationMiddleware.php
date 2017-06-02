<?php

namespace CayBua\Middleware;

use CayBua\Constants\Services;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconRest\Mvc\Plugin;
use PhalconRest\Api;
use PhalconApi\Constants\ErrorCodes;
use PhalconApi\Exception;

class AuthorizationMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute(Event $event, Api $api)
    {
        $collection = $api->getMatchedCollection();
        $endpoint = $api->getMatchedEndpoint();

        if (!$collection || !$endpoint) {
            return;
        }
        $request = Di::getDefault()->get(Services::REQUEST);
        $config = Di::getDefault()->get(Services::CONFIG);
        $accesstrustedkey = $request->getHeader('AccessTrustedKey');
        if (!empty($accesstrustedkey) && $accesstrustedkey == $config->get('authentication')->accesstrustedkey) {
            //Allow for server request
        } else {
            $allowed = $this->acl->isAllowed($this->userService->getRole(), $collection->getIdentifier(),
                $endpoint->getIdentifier());

            if (!$allowed) {
                throw new Exception(ErrorCodes::ACCESS_DENIED);
            }
        }
    }

    public function call(Micro $api)
    {
        return true;
    }
}