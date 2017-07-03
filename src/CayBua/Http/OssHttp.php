<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/3/17
 * Time: 11:32
 */
namespace CayBua\Http;

use CayBua\Constants\Services;
use Phalcon\Config;
use Phalcon\Di;
use Psr\Http\Message\ResponseInterface;

class OssHttp extends BaseHttp
{
    /**
     * OssHttp constructor.
     */
    public function __construct()
    {
        /** @var Config $config */
        $config = Di::getDefault()->get(Services::CONFIG);
        self::$serviceConfig = $config->get('services')['oss'];
        self::$serviceUrl = self::$serviceConfig['url'];
    }

    /**
     * @param $username
     * @param $password
     * @return ResponseInterface
     */
    public static function loginWithUsernameAndPassword($username, $password){
        $body = [
            'Authorization' => self::$serviceConfig['basicToken'],
            'query' => [
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password
            ]
        ];
        return self::post(self::$serviceConfig['action']['login'])->setBody($body)->response();
    }

    /**
     * Get user information from OSS with access token
     * @param $accessToken
     * @return ResponseInterface
     */
    public static function getUserInformationWithAccessToken($accessToken){
        $body = [
            'Content-Type' => 'application/json',
            'Authorization' => $accessToken
        ];
        return self::get(self::$serviceConfig['action']['userInformation'])->setBody($body)->response();
    }

}