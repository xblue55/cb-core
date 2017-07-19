<?php

namespace CayBua\User;

use CayBua\Constants\AclRoles;

use CayBua\Http\PublicHttp\UserPublicHttp;
use PhalconApi\User\Service as PhalconApiService;

class Service extends PhalconApiService
{
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
        $details = [];
        $userHttp = new UserPublicHttp();
        $token = $this->authManager->getSession()->getToken();
        $myUser = $userHttp->getUserInformationWithToken($token)->request(true);
        if (isset($myUser['data']['item']) && $myUser['data']['item']['id'] > 0) {
            $details = $myUser['data']['item'];
        }
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