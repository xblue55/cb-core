<?php

namespace CayBua\Auth;

use PhalconApi\Auth\Session;
use PhalconApi\Exception;
use PhalconApi\Constants\ErrorCodes;

class Manager extends \PhalconApi\Auth\Manager
{
    const LOGIN_DATA_EMAIL = 'email';

    /**
     * @param string $accountTypeName
     * @param string $email
     * @param string $password
     *
     * @return Session Created session
     * @throws /Exception
     *
     * Helper to login with email & password
     */
    public function loginWithEmailPassword($accountTypeName, $email, $password)
    {
        return $this->login($accountTypeName, [

            self::LOGIN_DATA_EMAIL => $email,
            self::LOGIN_DATA_PASSWORD => $password
        ]);
    }

    /**
     * @param string $token Token to authenticate with
     *
     * @return bool
     * @throws Exception
     */
    public function authenticateToken($token)
    {
        try {

            $session = $this->tokenParser->getSession($token);
        }
        catch (\Exception $e) {

            throw new Exception(ErrorCodes::AUTH_TOKEN_INVALID);
        }

        if (!$session) {
            return false;
        }

        if ($session->getExpirationTime() < time()) {

            throw new Exception(ErrorCodes::AUTH_SESSION_EXPIRED);
        }

        $session->setToken($token);

        // Authenticate identity
        $account = $this->getAccountType($session->getAccountTypeName());

        if (!$account) {
            throw new Exception(ErrorCodes::AUTH_SESSION_INVALID);
        }

        if (!$account->authenticate($session->getIdentity())) {

            throw new Exception(ErrorCodes::AUTH_TOKEN_INVALID);
        }

        $this->session = $session;

        return true;
    }
}