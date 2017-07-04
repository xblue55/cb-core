<?php

namespace CayBua\User;

use CayBua\Constants\AclRoles;
use CayBua\Http\UserHttp;

class Service extends \PhalconApi\User\Service
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
        $myUser = $userHttp->getUserInformationWithUserId($identity)->getParsingResponse();
        if (isset($myUser['data']['item']) && $myUser['data']['item']['id'] > 0) {
            $details = $myUser['data']['item'];
        }
        self::$detailsCache[$identity] = $details;
        return $details;
    }
}