<?php

namespace CayBua\User;

use CayBua\Constants\AclRoles;
use App\Model\User;
use CayBua\Mvc\BaseModel as BaseModel;

class Service extends \PhalconApi\User\Service
{
    protected $detailsCache = [];

    public function getRole()
    {
        /** @var User $userModel */

        $userModel = $this->getDetails();

        $role = AclRoles::UNAUTHORIZED;
        $userRole = ucfirst(strtolower($userModel->role));
        if($userModel && in_array($userRole, AclRoles::ALL_ROLES)){
            $role = $$userRole;
        }

        return $role;
    }

    protected function getDetailsForIdentity($identity)
    {
        if (array_key_exists($identity, $this->detailsCache)) {
            return $this->detailsCache[$identity];
        }
        $details = new BaseModel();
        $details->getData($identity);
        $this->detailsCache[$identity] = $details;

        return $details;
    }
}