<?php

namespace CayBua\Auth;

use CayBua\Constants\Services;
use Phalcon\Di;

class EmailAccountType implements \PhalconApi\Auth\AccountType
{
    const NAME = "username";

    public function login($data)
    {
        /** @var \Phalcon\Security $security */
        $security = Di::getDefault()->get(Services::SECURITY);

        $email = $data[Manager::LOGIN_DATA_EMAIL];
        $password = $data[Manager::LOGIN_DATA_PASSWORD];

        /** @var \App\Model\User $user */
        $user = \App\Model\User::findFirst([
            'conditions' => 'email = :email:',
            'bind' => ['email' => $email]
        ]);

        if (!$user) {
            return null;
        }

        if (!$security->checkHash($password, $user->password)) {
            return null;
        }

        return (string)$user->id;
    }

//    public function authenticate($identity)
//    {
//        return \App\Model\User::count([
//            'conditions' => 'id = :id:',
//            'bind' => ['id' => (int)$identity]
//        ]) > 0;
//    }
    public function authenticate($identity)
    {
        $pass = 0;
        $myUser = BaseModel::doRequest('GET', '/users/'.$identity);
        if (isset($myUser['data']['item']) && $myUser['data']['item']['id'] > 0) {
            $pass =1;
        }
        return $pass;
    }
}