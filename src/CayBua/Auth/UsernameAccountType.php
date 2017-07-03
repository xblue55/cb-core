<?php

namespace CayBua\Auth;

use CayBua\Constants\Services;
use CayBua\Http\OssHttp;
use CayBua\Services\ProfileService;
use CayBua\Services\UserService;
use Phalcon\Di;
use CayBua\Mvc\BaseModel as BaseModel;
use PhalconApi\Auth\AccountType;

class UsernameAccountType implements AccountType
{
    const NAME = "username";

    public function login($data)
    {
        /** @var \Phalcon\Security $security */
        $security = Di::getDefault()->get(Services::SECURITY);

        $username = $data[Manager::LOGIN_DATA_USERNAME];
        $password = $data[Manager::LOGIN_DATA_PASSWORD];

        /** Call api oss if get header login oss */
        $request = Di::getDefault()->get(Services::REQUEST);

        if (!$request->getHeader('AccessFromOssSystem') || !$request->getHeader('AccessFromOssSystem')) {
            $myUser = UserService::findUserWithUsername($username);
            return (string)$myUser->id;
        }

        $ossUserInfoReq =  OssHttp::loginWithUsernameAndPassword($username, $password);
        if ($ossUserInfoReq['status'] != '200' || !isset($ossUserInfoReq['data']['access_token'])) {
            return null;
        }

        /** Get user information from OSS with access token */
        $ossUserInfoReq = OssHttp::getUserInformationWithAccessToken($ossUserInfoReq['data']['access_token']);
        if (
            $ossUserInfoReq['status'] != '200' ||
            $ossUserInfoReq['data']['status'] != 'SUCCESS' ||
            empty($ossUserInfoReq['data']['records'])
        ) {
            return null;
        }

        $ossData = $ossUserInfoReq['data']['records'];

        $user = UserService::findUserWithUsername($username);
        if ($user) {
            $myUser = $user;
        } else {
            $myUser = UserService::createWithDataFromOss($ossData);
            $password = $ossData['id'] . $ossData['mail'];
            if (!$myUser) {
                return null;
            }
        }

        $ossData['user_id'] = $myUser->id;
        $ossData['access_token'] = $ossUserInfoReq['data']['access_token'];
        $myUserProfile = ProfileService::createOrUpdateWithDataFromOss($ossData);
        if (!$myUserProfile) {
            return null;
        }

        if (!$security->checkHash($password, $myUser->password)) {
            return null;
        }

        return (string)$myUser->id;
    }

    public function authenticate($identity)
    {
        $pass = false;
        $request = Di::getDefault()->get(Services::REQUEST);
        $config = Di::getDefault()->get(Services::CONFIG);
        $accesstrustedkey = $request->getHeader('AccessTrustedKey');
        if (!empty($accesstrustedkey) && $accesstrustedkey == $config->get('authentication')->accesstrustedkey) {
            //Allow for server request
            $pass = true;
        } else {
            $myUser = BaseModel::doRequest('GET', '/users/' . $identity);
            if (isset($myUser['data']['item']) && $myUser['data']['item']['id'] > 0) {
                $pass = true;
            }
        }
        return $pass;
    }
}