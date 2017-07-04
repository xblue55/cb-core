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
use Psr\Http\Message\ResponseInterface;

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
        $requestUrl = $this->serviceConfig['url'];
        if($this->actionUrl == '') {
            $requestUrl = $this->removeSlashEndOfUrl($this->serviceConfig['url']);
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

    /**
     * Parsing Response Data
     * @param ResponseInterface $response
     * @return array
     */
    public static function parsingResponse(ResponseInterface $response)
    {
        $responseData = [];
        $responseData['status'] = $response->getStatusCode();
        $responseData['contentType'] = $response->getHeader('Content-Type');
        $bodyStringData = $response->getBody()->getContents();
        if (stripos($responseData['contentType'][0], 'json') !== false) {
            $responseData['data'] = json_decode($bodyStringData, true);
        } else {
            $responseData['data'] = $bodyStringData;
        }
        return $responseData;
    }
}