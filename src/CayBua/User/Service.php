<?php

namespace CayBua\User;

use CayBua\Constants\AclRoles;
use CayBua\Http\UserHttp;

use PhalconApi\User\Service as PhalconApiService;

class Service extends PhalconApiService
{
    public static $detailsCache = [];

    public function getRole()
    {
        $userModel = $this->getDetails();
        $role = AclRoles::UNAUTHORIZED;
        if(!empty($userModel) && in_array(ucfirst(strtolower($userModel['role'])), AclRoles::ALL_ROLES)){
            $role = ucfirst(strtolower($userModel['role']));
        }
        return $role;
    }

    protected function getDetailsForIdentity($identity)
    {
        if (array_key_exists($identity, self::$detailsCache)) {
            return self::$detailsCache[$identity];
        }
        $details = [];
        $userHttp = new UserHttp();
        $token = $this->authManager->getSession()->getToken();
        $myUser = $userHttp->getUserInformationWithToken($token)->getParsingResponse();
        if (isset($myUser['data']['item']) && $myUser['data']['item']['id'] > 0) {
            $details = $myUser['data']['item'];
        }
        self::$detailsCache[$identity] = $details;
        return $details;
    }

    /**
     * @return mixed
     */
    public function getTickets(){
        $user = $this->getDetails();
        return $user['tickets'];
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @return bool
     */
    public function allowRbacPermission($controllerName, $actionName)
    {
        $resource = $controllerName . '.' . $actionName;
        $tickets = $this->getTickets();
        return in_array($resource, $tickets);
    }

}