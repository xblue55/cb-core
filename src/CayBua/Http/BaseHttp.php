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
    public $serviceConfig;
    public $serviceUrl;
    public $method;
    public $actionUrl;
    public $body;

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function get($actionUrl = '')
    {
        $this->method = 'GET';
        $this->actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function post($actionUrl = '')
    {
        $this->method = 'POST';
        $this->actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function put($actionUrl = '')
    {
        $this->method = 'PUT';
        $this->actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param string $actionUrl
     * @return $this
     */
    public function delete($actionUrl = '')
    {
        $this->method = 'DELETE';
        $this->actionUrl = $actionUrl;
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }


    /**
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function response()
    {
        if($this->actionUrl == '') {
            $requestUrl = $this->removeSlashEndOfUrl($this->serviceUrl);
        }else{
            $requestUrl = $this->serviceUrl . $this->actionUrl;
        }
        $client = new GuzzleHttp\Client(
            [
                'base_uri' => $requestUrl
            ]
        );
        try {
            $response = $client->request($this->method, $this->actionUrl, $this->body);
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