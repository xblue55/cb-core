<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 8/23/17
 * Time: 11:34
 */

namespace CayBua\Http;

use CayBua\Constants\ConfigConstants;
use CayBua\Constants\Services;
use Phalcon\Config;
use Phalcon\Di;

class FileHttp extends BaseHttp
{

    /** @var Config $config */
    protected $config;

    /**
     * FileHttp constructor.
     */
    public function __construct()
    {
        $this->config = Di::getDefault()->get(Services::CONFIG);
        $this->setServiceConfig($this->config->get(ConfigConstants::SERVICES)['file']);
    }

    /**
     * @param $tmpPath
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function uploadFileWithUniqueKeyDownload($tmpPath){
        $body = [
            'headers' => [
                'Access-Trusted-Key' => $this->config->get(ConfigConstants::ACCESS_TRUSTED_KEY)
            ],
            'multipart' => [
                [
                    'contents' => fopen($tmpPath, 'r')
                ]
            ]
        ];
        return $this
            ->get($this->serviceConfig['action']['upload-with-unique-key-download'])
            ->setBody($body)
            ->request(true);
    }
}