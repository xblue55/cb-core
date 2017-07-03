<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/3/17
 * Time: 11:40
 */
namespace CayBua\Services;

use CayBua\Constants\AclRoles;
use CayBua\Constants\Services;
use CayBua\Model\User;
use Phalcon\Di;

class UserService
{

    public static function createWithDataFromOss($data){
        $user = new User();
        $security = Di::getDefault()->get(Services::SECURITY);
        $user->username = $data['id'];
        $user->email = $data['mail'];
        $user->phone = $data['phone'];
        $user->address = $data['address'];
        $user->password = $security->hash($data['id'] . $data['mail']);
        $user->cid = $data['mainCompany'];
        $user->role = AclRoles::USER;
        if (!$user->save()) {
            return null;
        }
        return $user;
    }

    /**
     * @param $username
     * @return User | mixed
     */
    public static function findUserWithUsername($username){
        return User::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);
    }
}