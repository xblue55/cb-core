<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/3/17
 * Time: 11:07
 */

namespace CayBua\Http;

use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;

abstract class BaseHttp
{
    public static $serviceConfig;
    public static $serviceUrl;
    public static $method;
    public static $actionUrl;
    public static $body;

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function get($actionUrl = '')
    {
        self::$method = 'GET';
        self::$actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function post($actionUrl = '')
    {
        self::$method = 'POST';
        self::$actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function put($actionUrl = '')
    {
        self::$method = 'PUT';
        self::$actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function delete($actionUrl = '')
    {
        self::$method = 'DELETE';
        self::$actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        self::$body = $body;
        return $this;
    }


    /**
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function response()
    {
        if(self::$actionUrl == '') {
            $requestUrl = $this->removeSlashEndOfUrl(self::$serviceUrl);
        }else{
            $requestUrl = self::$serviceUrl . self::$actionUrl;
        }
        $client = new GuzzleHttp\Client(
            [
                'base_uri' => $requestUrl
            ]
        );
        try {
            $response = $client->request(self::$method, self::$actionUrl, self::$body);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }

    /**
     * Remove end slash of service url
     *
     * @param string $url
     * @return string
     */
    protected function removeSlashEndOfUrl($url)
    {
        return substr($url, 0, -1);
    }
}