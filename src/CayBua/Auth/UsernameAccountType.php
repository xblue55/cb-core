<?php

namespace CayBua\Auth;

use CayBua\Constants\Services;
use Phalcon\Di;
use CayBua\Mvc\BaseModel as BaseModel;

class UsernameAccountType implements \PhalconApi\Auth\AccountType
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
        $config = Di::getDefault()->get(Services::CONFIG);
        if ($request->getHeader('AccessFromOssSystem') && $request->getHeader('AccessFromOssSystem') == true) {
            $ossLoginUrl = $config->get('ossapi')->loginurl . '?grant_type=password&username='.$username.'&password=' . $password;
            BaseModel::$baseurl = $config->get('ossapi')->baseurl;
            $headers = array(
                'Authorization' => $config->get('ossapi')->basictoken
            );
            $ossUserInfoReq = BaseModel::doRequest('POST', $ossLoginUrl, $headers, false, false);
            if ($ossUserInfoReq['status'] == '200' && isset($ossUserInfoReq['data']['access_token'])) {
                $ossAccessToken = $ossUserInfoReq['data']['access_token'];
                /** Get userinfo from oss accesstoken */
                $headers = [
                    'Authorization' => $ossAccessToken,
                    'Content-Type' => 'application/json'
                ];
                $ossUserInfoReq = BaseModel::doRequest('GET', $config->get('ossapi')->userinfourl, $headers, false, false);
                if ($ossUserInfoReq['status'] == '200'
                    && $ossUserInfoReq['data']['status'] == 'SUCCESS' && !empty($ossUserInfoReq['data']['records'])) {
                    $ossData = $ossUserInfoReq['data']['records'];
                    $myUser = new \App\Model\User();

                    $user = \App\Model\User::findFirst([
                        'conditions' => 'username = :username:',
                        'bind' => ['username' => $username]
                    ]);
                    if ($user) {
                        $myUser = $user;
                    }
                    $myUser->username = $ossData['id'];
                    $myUser->email = $ossData['mail'];
                    $myUser->phone = $ossData['phone'];
                    $myUser->address = $ossData['address'];
                    $myUser->password = $security->hash($ossData['id'] . $ossData['mail']);
                    $myUser->cid = $ossData['mainCompany'];
                    $myUser->role = \CayBua\Constants\AclRoles::USER;
                    if ($myUser->save()) {
                        $password = $ossData['id'] . $ossData['mail'];
                        /** Save info in user profile */
                        $myUserProfile = new \App\Model\Profile();
                        $userprofile = \App\Model\Profile::findFirst(['conditions' => 'id = :id:', 'bind' => ['id' => $myUser->id]]);
                        if ($userprofile) {
                            $myUserProfile = $userprofile;
                        }
                        $myUserProfile->id = $myUser->id;
                        $myUserProfile->fullname = $ossData['name'];
                        $myUserProfile->address = $ossData['address'];
                        $myUserProfile->oauthpartner = \App\Model\Profile::OAUTH_PARTNER_OSS;
                        $myUserProfile->oauthuid = $ossData['id'];
                        $myUserProfile->oauthaccesstoken = $ossAccessToken;
                        $myUserProfile->save();
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            /** @var \App\Model\User $user */
            $myUser = \App\Model\User::findFirst([
                'conditions' => 'username = :username:',
                'bind' => ['username' => $username]
            ]);
        }
        if (!$myUser) {
            return null;
        }

        if (!$security->checkHash($password, $myUser->password)) {
            return null;
        }

        return (string)$myUser->id;
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
