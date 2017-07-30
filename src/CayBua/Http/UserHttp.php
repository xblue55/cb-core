<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/30/17
 * Time: 21:20
 */

namespace CayBua\Http;

use CayBua\Constants\Services;
use Phalcon\Config;
use Phalcon\Di;

class UserHttp extends BaseHttp
{
    /**
     * UserHttp constructor.
     */
    public function __construct()
    {
        /** @var Config $config */
        $config = Di::getDefault()->get(Services::CONFIG);
        $this->serviceConfig = $config->get('services')['user'];
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
    public function getUserInformationWithUserID($userID)
    {
        return $this
            ->get($userID)
            ->request(true);
    }

    /**
     * @param $userID
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function getUseProfileWithUserID($userID){
        return $this
            ->get($this->serviceConfig['action']['profile'].'/'.$userID)
            ->request(true);
    }
}