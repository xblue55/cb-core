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
    public $responseData;

    public static $dataCache = [];

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
     * @param bool $parsingResponse
     * @return mixed|null|ResponseInterface
     */
    public function request($parsingResponse = false)
    {
        $requestUrl = $this->serviceConfig['url'];
        if ($this->actionUrl == '') {
            $requestUrl = $this->removeSlashEndOfUrl($this->serviceConfig['url']);
        }
        $client = new GuzzleHttp\Client(
            [
                'base_uri' => $requestUrl
            ]
        );
        $key = md5($this->method . $requestUrl . $this->actionUrl . json_encode($this->body));
        if ($parsingResponse) {
            if ($this->hasCache($key)) {
                return $this->readCache($key);
            }
        }
        try {
            if (empty($this->body)) {
                $response = $client->request($this->method, $this->actionUrl);
            } else {
                $response = $client->request($this->method, $this->actionUrl, $this->body);
            }
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }
        $this->writeCache($key, self::parsingResponse($response));
        if ($parsingResponse) {
            return self::$dataCache[$key];
        } else {
            return $response;
        }
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
        $responseData['statusCode'] = $response->getStatusCode();
        $responseData['contentType'] = $response->getHeader('Content-Type');
        $bodyStringData = $response->getBody()->getContents();
        if (stripos($responseData['contentType'][0], 'json') !== false) {
            $responseData['data'] = json_decode($bodyStringData, true);
        } else {
            $responseData['data'] = $bodyStringData;
        }
        return $responseData;
    }

    /**
     * @param $key
     * @param $data
     */
    private function writeCache($key, $data)
    {
        self::$dataCache[$key] = $data;
    }

    /**
     * @param $key
     * @return mixed
     */
    private function readCache($key)
    {
        return self::$dataCache[$key];
    }

    /**
     * @param $key
     * @return bool
     */
    private function hasCache($key)
    {
        if (isset(self::$dataCache[$key]) && !empty(self::$dataCache[$key])) {
            return true;
        }
        return false;
    }

    public function buildQueryString($params, $page = 1, $recordPerPage = 0)
    {
        $queryString = '';
        if ($recordPerPage > 0) {
            $queryString .= strpos($queryString, '?') ? '&' : '?';
            $queryString .= 'limit=' . $recordPerPage;
        }
        if ($page > 1) {
            $offset = ($page - 1) * $recordPerPage;
            $queryString .= strpos($queryString, '?') ? '&' : '?';
            $queryString .= 'offset=' . $offset;
        }
        foreach ($params as $key => $value) {
            if (in_array($key, $this->getQueryKey()) and !empty($value)) {
                $queryString .= strpos($queryString, '?') ? '&' : '?';
                if ($key == 'fields') {
                    $queryString .= $key . '=' . implode(',', $value);
                } else {
                    $queryString .= $key . '=' . json_encode($value);
                }
            }
        }

        return $queryString;
    }
    private function getQueryKey()
    {
        return [
            'having',
            'where',
            'fields',
            'or',
            'in',
            'sort',
        ];
    }
}