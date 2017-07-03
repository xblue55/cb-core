<?php

namespace CayBua\Model;

class Profile extends BaseModel
{
    const OAUTH_PARTNER_EMPTY = 0;
    const OAUTH_PARTNER_FACEBOOK = 1;
    const OAUTH_PARTNER_YAHOO = 2;
    const OAUTH_PARTNER_GOOGLE = 3;
    const OAUTH_PARTNER_OSS = 4;

    const GENDER_UNKNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    public $id;
    public $fullname;
    public $birthday;
    public $phone;
    public $address;
    public $country;
    public $city;
    public $district;
    public $website;
    public $bio;
    public $function;
    public $activatedcode;
    public $oauthpartner;
    public $oauthuid;
    public $oauthaccesstoken;
    public $newnotification;
    public $newmessage;
    public $facebook;
    public $youtube;
    public $googleplus;
    public $instagram;
    public $datelastlogin;

    public function getSource()
    {
        return 'fly_user_profile';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
                'id' => 'id',
                'fullname' => 'fullname',
                'birthday' => 'birthday',
                'phone' => 'phone',
                'address' => 'address',
                'country' => 'country',
                'city' => 'city',
                'district' => 'district',
                'website' => 'website',
                'bio' => 'bio',
                'function' => 'function',
                'activatedcode' => 'activatedcode',
                'oauth_partner' => 'oauthpartner',
                'oauth_uid' => 'oauthuid',
                'oauth_accesstoken' => 'oauthaccesstoken',
                'newnotification' => 'newnotification',
                'newmessage' => 'newmessage',
                'facebook' => 'facebook',
                'youtube' => 'youtube',
                'twitter' => 'twitter',
                'googleplus' => 'googleplus',
                'instagram' => 'instagram',
                'datelastlogin' => 'datelastlogin'
            ];
    }

    public function initialize()
    {
        $this->belongsTo('id', User::class, 'id', [
            'alias' => User::class,
        ]);
    }
}