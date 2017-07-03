<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/3/17
 * Time: 11:41
 */

namespace CayBua\Services;

use CayBua\Model\Profile;

class ProfileService
{

    /**
     * @param $id
     * @return \Phalcon\Mvc\Model
     */
    public static function findWithIdentity($id)
    {
        return Profile::findFirst(
            [
                'conditions' => 'id = :id:',
                'bind' => [
                    'id' => $id
                ]
            ]
        );
    }

    /**
     * Create or Update user Profile
     * @param $data
     * @return Profile|null|\Phalcon\Mvc\Model
     */
    public static function createOrUpdateWithDataFromOss($data)
    {
        $profile = new Profile();
        $userprofile = ProfileService::findWithIdentity($data['user_id']);
        if ($userprofile) {
            $profile = $userprofile;
        }
        $profile->id = $data['user_id'];
        $profile->fullname = $data['name'];
        $profile->address = $data['address'];
        $profile->oauthpartner = Profile::OAUTH_PARTNER_OSS;
        $profile->oauthuid = $data['id'];
        $profile->oauthaccesstoken = $data['access_token'];
        if (!$profile->save()) {
            return null;
        }
        return $profile;
    }
}