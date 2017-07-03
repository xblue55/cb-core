<?php

namespace CayBua\Auth;

use CayBua\Constants\Services;
use CayBua\Mvc\BaseModel;
use Phalcon\Di;
use \CayBua\Model\User;

class EmailAccountType implements \PhalconApi\Auth\AccountType
{
    const NAME = "username";

    public function login($data)
    {
        /** @var \Phalcon\Security $security */
        $security = Di::getDefault()->get(Services::SECURITY);

        $email = $data[Manager::LOGIN_DATA_EMAIL];
        $password = $data[Manager::LOGIN_DATA_PASSWORD];

        /** @var \CayBua\Model\User $user */
        $user = User::findFirst([
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

    public function authenticate($identity)
    {
        $pass = 0;
        $request = Di::getDefault()->get(Services::REQUEST);
        // var_dump($request->getHeaders());
        $config = Di::getDefault()->get(Services::CONFIG);
        $accesstrustedkey = $request->getHeader('AccessTrustedKey');
        if (!empty($accesstrustedkey) && $accesstrustedkey == $config->get('authentication')->accesstrustedkey) {
            $pass = 1;
        } else {
            $myUser = BaseModel::doRequest('GET', '/users/'.$identity);
            if (isset($myUser['data']['item']) && $myUser['data']['item']['id'] > 0) {
                $pass =1;
            }
        }
        return $pass;
    }
}