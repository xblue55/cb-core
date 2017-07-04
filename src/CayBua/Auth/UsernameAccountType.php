<?php

namespace App\Auth;

use CayBua\Constants\Services;

use Phalcon\Di;
use PhalconApi\Auth\AccountType;

class UsernameAccountType implements AccountType
{
    const NAME = 'username';


    public function login($data)
    {

    }

    /**
     * @param string $identity
     * @return bool
     */
    public function authenticate($identity)
    {
        // Gọi service User để lấy authenticate.
        return true;
    }
}