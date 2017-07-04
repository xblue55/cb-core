<?php

namespace CayBua\Auth;

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
        return true;
    }
}