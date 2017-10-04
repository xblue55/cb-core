<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/26/17
 * Time: 15:08
 */

namespace CayBua\Middleware;

use CayBua\Api;
use CayBua\Mvc\Plugin;
use CayBua\Constants\AclRoles;
use CayBua\Constants\Services;

use CayBua\User\Service;
use PhalconApi\Exception;
use PhalconApi\Constants\ErrorCodes;

use Phalcon\Mvc\Micro;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro\MiddlewareInterface;

class RbacMiddleware extends Plugin implements MiddlewareInterface
{

    public function beforeExecuteRoute(Event $event, Api $api)
    {
        $allowed = true;
        $role = $this->userService->getRole();
        if (($role != AclRoles::UNAUTHORIZED) && ($role != AclRoles::LOCAL_SERVICE)) {
            /** @var Service $userService */
            $userService = $this->di->get(Services::USER_SERVICE);
            $uri = $api->request->get();
            $method = $api->request->getMethod();
            $allowed = $userService->allowRbacPermission($method, $uri['_url']);
        }
        if (!$allowed) {
            throw new Exception(ErrorCodes::ACCESS_DENIED);
        }
    }

    /**
     * @param Micro $api
     * @return bool
     */
    public function call(Micro $api)
    {
        return true;
    }
}