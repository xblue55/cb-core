<?php

namespace CayBua\Model;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;

class User extends BaseModel
{
    public $cid;
    public $id;
    public $udid;
    public $username;
    public $slug;
    public $phone;
    public $email;
    public $password;
    public $role;
    public $avatar;
    public $cover;
    public $status;
    public $address;

    public function getSource()
    {
        return 'fly_user';
    }

    public function columnMap()
    {
        return parent::columnMap() +[
                'cid' => 'cid',
                'id' => 'id',
                'udid' => 'udid',
                'username' => 'username',
                'slug' => 'slug',
                'phone' => 'phone',
                'email' => 'email',
                'password' => 'password',
                'role' => 'role',
                'avatar' => 'avatar',
                'cover' => 'cover',
                'status' => 'status',
                'address' => 'address'
            ];
    }

    public function initialize() {

        $this->hasOne('id', Profile::class, 'id', [
            'alias' => Profile::class,
        ]);
    }

    /**
     * @return bool
     */
    public function validation()
    {
        $validator = new Validation();
        $validator->add(
            [
                'username',
                'slug',
                'email'
            ],
            new Uniqueness([
                'message' => "The username must be unique",
            ])
        );
        return $this->validate($validator);
    }
}