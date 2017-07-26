<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/19/17
 * Time: 11:47
 */

namespace CayBua\Http\PrivateHttp;

use CayBua\Constants\Services;
use CayBua\Http\BaseHttp;
use Phalcon\Config;
use Phalcon\Di;

class UserPrivateHttp extends BaseHttp
{
    /**
     * UserHttp constructor.
     */
    public function __construct()
    {
        /** @var Config $config */
        $config = Di::getDefault()->get(Services::CONFIG);
        $this->serviceConfig = $config->get('services_private')['user'];
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
            ->get($this->serviceConfig['action']['profile'].$userID)
            ->request(true);
    }
}