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
        $token = $this->authManager->getSession()->getToken();
        $userHttp = new UserPublicHttp();
        $myUser = $userHttp->getUserInformationWithToken($token);
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
     * @param string $domainName
     * @param string $controllerName
     * @param string $actionName
     * @return bool
     */
    public function allowRbacPermission($domainName, $controllerName, $actionName)
    {
        $userRole = $this->getRole();
        if($userRole == AclRoles::ADMINISTRATOR){
            return true;
        } else {
            $resource = $domainName . '.' . $controllerName . '.' . $actionName;
            $tickets = $this->getTickets();
            return in_array($resource, $tickets);
        }
    }

}