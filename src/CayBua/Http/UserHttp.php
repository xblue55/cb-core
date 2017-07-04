<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/3/17
 * Time: 11:08
 */

namespace CayBua\Http;

use CayBua\Constants\Services;
use Phalcon\Config;
use Phalcon\Di;

class UserHttp extends BaseHttp
{
    /**
     * PrivateApiHttp constructor.
     */
    public function __construct()
    {
        /** @var Config $config */
        $config = Di::getDefault()->get(Services::CONFIG);
        $this->serviceConfig = $config->get('services')['user'];
    }

    /**
     * @param $userId
     * @return $this
     */
    public function getUserInformationWithUserId($userId)
    {
        $actionUrl = $this->serviceConfig['action']['me'] . '/' . $userId;
        return
            $this->get($actionUrl)
                ->request();
    }
}