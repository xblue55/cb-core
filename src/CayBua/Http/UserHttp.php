<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/30/17
 * Time: 21:20
 */

namespace CayBua\Http;

use CayBua\Constants\ConfigConstants;
use CayBua\Constants\Services;
use Phalcon\Config;
use Phalcon\Di;

class UserHttp extends BaseHttp
{
    /** @var Config $config */
    protected $config;

    /**
     * UserHttp constructor.
     */
    public function __construct()
    {
        $this->config = Di::getDefault()->get(Services::CONFIG);
        $this->setServiceConfig($this->config->get(ConfigConstants::SERVICES)['user']);
    }

    /**
     * @param $token
     * @return $this
     */
    public function getUserInformationWithToken($token)
    {
        $body = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'query' => [
                'include' => 'roles,services,tickets',
            ]
        ];
        return
            $this
                ->get($this->serviceConfig['action']['me'])
                ->setBody($body)
                ->request(true);
    }

    /**
     * @param $userID
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function getUseProfileWithUserID($userID){
        $body = [
            'headers' => [
                'Access-Trusted-Key' => $this->config->get(ConfigConstants::ACCESS_TRUSTED_KEY)
            ]
        ];
        return $this
            ->get($this->serviceConfig['action']['profile'].'/'.$userID)
            ->setBody($body)
            ->request(true);
    }
}