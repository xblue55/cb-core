<?php

namespace CayBua\Mvc;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\BadResponseException;
class BaseModel extends \Phalcon\Mvc\Model
{
    /**
     * Base URL of all restful request
     * This variable can be overwrite by environment variable 'REST_API'
     */
    public static $baseurl = 'http://113.164.243.166';

    /**
     * This property will be set on each model to point to correct api point
     * Full request url will be combine by baseurl and serviceurl.
     * If service url is start with 'http', the full request url will be not combined with base url
     */
    public static $serviceurl = '/users';

    /**
     * Toggle the debug mode for request
     *
     * @var bool
     */
    public static $debug = false;

    /**
     * This property will be set on almost request for authorization (in request headers)
     * This variable can be overwrite by environment variable 'REST_AUTH_TOKEN'
     */
    public static $authorizationToken = '';

    /**
     * This property will be set on almost request for inter-service request (in request headers)
     * This variable SHOULD be set by environment variable 'ACCESS_TRUSTED_KEY'
     * @var string
     */
    public static $accessTrustedKey = '';
    /**
     * Request timeout for request to api service. in seconds
     */
    public static $requestTimeOut = 15;

    /**
     * Helper property for restore requesttimeout when using set temporary timeout
     */
    public static $requestTimeoutTmp = 0;

    /**
     * @var string Used to identifier current request in sub request, generated in bootstrap
     */
    public static $myChainIdentifier = '';

    /**
     * @var string Chain Identifier received from request
     */
    public static $requestChainIdentifier = '';

    public static $client = null;

    /**
     * @var array Contains all the promises for async request
     */
    public static $promises = array();

    public $di = null;

    public function initialize()
    {
        self::initClient();
        $this->di = \Phalcon\DI::getDefault();
    }

    public static function initClient()
    {
        //Init request client (in this case, GuzzleHttp)
        if (is_null(self::$client)) {
            self::$client = new Client(array('base_uri' => self::$baseurl));
        }
    }

    public static function getAuthorizationToken()
    {
        return self::$authorizationToken;
    }

    public static function getAccessTrustedKey()
    {
        return self::$accessTrustedKey;
    }


    public static function doRequest(
        $method,
        $url = '',
        $headers = array(),
        $useJwt = true,
        $useAccessTrusted = true,
        $requestbody = null,
        $asyncItemId = 0
    ) {
        //Get url base on parameter
        if ($url == '') {
            $url = self::$serviceurl;
        }

        if ($useJwt) {
            $headers['Authorization'] = self::getAuthorizationToken();
        }

        if ($useAccessTrusted) {
            $headers['AccessTrustedKey'] = self::getAccessTrustedKey();
        }

        //Append chain-identifier for tracking
        if (self::$requestChainIdentifier == '') {
            $headers['Chain-Identifier'] = self::$myChainIdentifier;
        } else {
            $headers['Chain-Identifier'] = self::$requestChainIdentifier . ', ' . self::$myChainIdentifier;
        }

        //instance client object
        self::initClient();

        /** @var \GuzzleHttp\Client $client */
        $client = self::$client;

        $request = new Request($method, $url, $headers, $requestbody);

        if ($asyncItemId > 0) {

            $currentCalledClass = get_called_class();
            BaseModel::$promises[$currentCalledClass][$asyncItemId] = $client->sendAsync($request, array(
                'timeout' => self::$requestTimeOut
            ));

            return array($currentCalledClass, $asyncItemId);
        } else {
            try {

                $response = $client->send($request, array(
                    'timeout' => self::$requestTimeOut,
                    'debug' => self::$debug
                ));

                return self::parsingResponse($response);

            } catch (BadResponseException $e) {
                return self::parsingResponse($e->getResponse());
            }
        }
    }

    public static function parsingResponse(ResponseInterface $response)
    {
        $responseData = array();

        $responseData['status'] = $response->getStatusCode(); // 200
        $responseData['contenttype'] = $response->getHeader('Content-Type');
        $bodystringdata = $response->getBody()->getContents();
        if (stripos($responseData['contenttype'][0], 'json') !== false) {
            $responseData['data'] = json_decode($bodystringdata, true);
        } else {
            $responseData['data'] = $bodystringdata;
        }

        return $responseData;
    }

    /**
     * Used to extract CURRENT_PAGE and RECORD_PER_PAGE from limit string
     * Ex: if $limitString is '100, 50', it will return array with
     * CURRENT_PAGE = 3 & RECORD_PER_PAGE = 50
     * @param $limitString
     * @return mixed ($currentPage, $recordPerPage)
     */
    public static function extractPageInfoFromLimit($limitString)
    {
        $currentPage = 0;
        $recordPerPage = 0;

        if (is_numeric($limitString)) {
            $currentPage = 1;
            $recordPerPage = (int)$limitString;

        } else {
            $parts = explode(',', $limitString);
            if (count($parts) == 2) {
                $recordPerPage = (int)trim($parts[1]);
                if ($recordPerPage > 0) {
                    $currentPage = (int)trim($parts[0]) / $recordPerPage + 1;
                }
            }
        }


        return array($currentPage, $recordPerPage);
    }

    /**
     * Request to Restful API to get Data
     * @param $id
     * @param bool $loadFromCache
     * @param array $querydata
     */
    public function getData($id , $loadFromCache = false, $querydata = array())
    {
//        if ($loadFromCache) {
//            $myCacher = new Cacher(static::cacheKey($id));
//            $row = $myCacher->get();
//            if (!empty($row)) {
//                $this->getDataByArrayCache($row);
//            }
//        }

        //Even if with load from cache, id is still zero, we get from remote data


        $url = trim(static::$serviceurl, '/') . '/' . $id;

        //call to get response data
        if (!empty($querydata)) {
            $url .= '?' . http_build_query($querydata);
        }

        try {
            $response = self::doRequest(
                'GET',
                $url
            );
            //request success
            if ($response['status'] == '200') {

                if (!empty($response['data'])) {
                    $this->getDataByJson($response['data']);
                }

            }
        } catch (\Exception $e) {
            $error = $e->getMessage() . ' (Rest::getData: ' . $url . ')';
            $this->di->get('logger')->notice('[debug:rest:base]' . $error
                . '. IP: ' . $this->di->get('request')->getClientAddress()
                . '. Method: ' . $this->di->get('request')->getMethod()
                . '. Chain: ' . BaseModel::$requestChainIdentifier
                . '. Query: ' . json_encode($this->di->get('request')->getQuery())
                . '. Body: ' . json_encode($this->di->get('request')->getRawBody()));
        }

    }


    /**
     * Default: Set object properties from array input. the key of array is the property name.
     * @param array $jsonData
     */
    public function getDataByJson($jsonData)
    {
        if (is_array($jsonData)) {
            foreach ($jsonData as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function getDataByArrayCache($row)
    {

    }

    /**
     * Loop through all properties and return as array
     * @return array
     */
    public function getJsonData()
    {
        return get_object_vars($this);
    }

    /**
     * Base method for all derived class to get all records
     * @param $formData
     * @param string $limitString
     * @param string $serviceurl
     * @return integer
     */
    public static function countItems($formData, $limitString = '', $serviceurl = '')
    {
        //get default serviceurl from static inherit class
        if ($serviceurl == '') {
            $serviceurl = static::$serviceurl;
        }

        if ($limitString != '') {
            list($currentPage, $recordPerPage) = self::extractPageInfoFromLimit($limitString);
            if ($currentPage > 0 && $recordPerPage > 0) {
                $formData['page'] = $currentPage;
                $formData['limit'] = $recordPerPage;
            }
        }

        //important flag to get number only
        $formData['totalonly'] = 1;

        $serviceurl = $serviceurl . '?' . http_build_query($formData);
        $responseData = self::doRequest('GET', $serviceurl);

        return (int)$responseData['data']['total'];
    }

    /**
     * Base method for all derived class to get all records
     * @param $formData
     * @param $sortby
     * @param $sorttype
     * @param string $limitString
     * @param string $serviceurl
     * @return array
     */
    public static function getItems($formData, $sortby, $sorttype, $limitString = '', $serviceurl = '')
    {
        $items = array();

        if ($sortby != '') {
            $formData['sort_by'] = $sortby;
        }

        if ($sorttype != '') {
            $formData['sort_type'] = $sorttype;
        }

        //get default serviceurl from static inherit class
        if ($serviceurl == '') {
            $serviceurl = static::$serviceurl;
        }

        if ($limitString != '') {
            list($currentPage, $recordPerPage) = self::extractPageInfoFromLimit($limitString);
            if ($currentPage > 0 && $recordPerPage > 0) {
                $formData['page'] = $currentPage;
                $formData['limit'] = $recordPerPage;
            }
        }

        $serviceurl = $serviceurl . '?' . http_build_query($formData);
        $responseData = self::doRequest('GET', $serviceurl);

        if (isset($responseData['data']['items']) && count($responseData['data']['items']) > 0) {
            foreach ($responseData['data']['items'] as $jsonData) {
                $myObject = new static();
                $myObject->getDataByJson($jsonData);
                $items[] = $myObject;
            }
        }

        return $items;
    }

    /**
     * Base method for all derived class to get all records
     * @param string $serviceurl
     * @return array
     */
    public static function getRawItems($formData = array(), $serviceurl = '')
    {
        if ($serviceurl == '') {
            $serviceurl = static::$serviceurl;
        }

        $serviceurl = $serviceurl . '?' . http_build_query($formData);
        $responseData = self::doRequest('GET', $serviceurl);

        $data  = array();
        if (!empty($responseData['data'])) {
            $data = $responseData['data'];
        }

        return $data;
    }

    /**
     * Get async data from list of item, and get ids from $selectedProperty
     * @param $items
     * @param $selectedProperty, if empty, the items will be id list
     * @return self[] $data
     */
    public static function getDataAsync($items, $selectedProperty)
    {
        $data = array();

        // If selected property is empty,
        //it;s mean items will be the id list
        if ($selectedProperty == '') {
            $ids = $items;
        } else {
            $ids = self::getPropValues($items, $selectedProperty);
        }

        if (is_array($ids)) {

            $currentCalledClass = get_called_class();

            $missCount = 0;
            $dataFromCache = $dataFromRemote = array();

            //Remove duplicate ids
            $ids = array_unique($ids);

            //Loop through all ids and register request async
            foreach ($ids as $id) {

                //Try to get from cache first ^^
                $myCacher = new Cacher(static::cacheKey($id));
                $row = $myCacher->get();


                if (!$row) {
                    //call to get promise async
                    self::doRequest(
                        'GET',
                        trim(static::$serviceurl, '/') . '/' . $id,
                        array(),
                        true,
                        true,
                        array(),
                        $id
                    );
                    $missCount++;
//                    echo 'MISS.';

                } else {
                    //Init data from cache
                    /** @var self $obj */
                    $obj = new $currentCalledClass();
                    $obj->getDataByArrayCache($row);
                    $dataFromCache[$id] = $obj;
//                    echo 'HIT.';
                }
            }

            //Start dispatch all async request to get response
            if ($missCount > 0) {
                $dataFromRemote = self::startDataAsync();
            }

            //array_merge do not preserve the numberic key, so, we use plus (union operator)
            $data = $dataFromRemote + $dataFromCache;
        }

        return $data;
    }

    /**
     * @return self[]
     */
    public static function startDataAsync()
    {
        $data = array();
        try {
            $currentCalledClass = get_called_class();
            $results = Promise\unwrap(Base::$promises[$currentCalledClass]);

            //Loop through all response
            /**
             * @var string $asyncName
             * @var ResponseInterface $response
             */
            foreach ($results as $asyncItemId => $response) {
                if ($response->getStatusCode() == 200) {
                    $response = self::parsingResponse($response);

                    $obj = new $currentCalledClass();
                    foreach ($response['data'] as $key => $value) {
                        $obj->$key = $value;
                    }
                    $data[$asyncItemId] = $obj;
                }
            }

        } catch (\Exception $e) {
            //die($e->getMessage());
        }

        return $data;
    }

    /**
     * Return array of value of one property of objectlist
     * @param self[] $objList
     * @param $property
     * @return array
     */
    public static function getPropValues($objList, $property)
    {
        $values = array();

        foreach ($objList as $myObj) {
            if (property_exists($myObj, $property)) {
                $values[] = $myObj->$property;
            }
        }

        return $values;
    }

    public function copy(\stdClass $object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function cacheKey($id)
    {
        return $id;
    }

    /**
     * Base function for add record via restful POST
     *
     * @param $jsonData
     * @param string $serviceurl
     * @param array $headers
     * @param array $error
     * @return int
     */
    public static function doAdd($jsonData, $serviceurl = '', $headers = array(), &$error = array())
    {
        //ID of successfully added record
        $objectId = 0;

        //set default serviceurl from current sdk serviceurl
        if ($serviceurl == '') {
            $serviceurl = static::$serviceurl;
        }

        //set default headers to json data
        if (empty($headers)) {
            $headers = array(
                'Content-type' => 'application/json'
            );
        }

        try {
            //Do request and get response with submit data
            $response = self::doRequest('POST', $serviceurl, $headers, true, true, json_encode($jsonData));

            //request success
            if ($response['status'] == '200' || $response['status'] == '201') {
                if (is_array($response['data']) && isset($response['data']['id'])) {
                    $objectId = $response['data']['id'];
                } else {
                    $error[] = 'error_id_not_found_in_response';
                }

            } else {
                $error = self::parsingErrorFromResponse($response);
            }
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        }

        return $objectId;
    }

    /**
     * Base function for update record via restful PUT. Return BOOL
     *
     * @param $jsonData
     * @param string $serviceurl
     * @param array $headers
     * @param array $error
     * @return boolean
     */
    public static function doUpdate($jsonData, $serviceurl = '', $headers = array(), &$error = array())
    {
        //ID of successfully updated record
        $objectId = 0;

        //set default serviceurl from current sdk serviceurl
        if ($serviceurl == '') {
            $serviceurl = static::$serviceurl;
        }

        //set default headers to json data
        if (empty($headers)) {
            $headers = array(
                'Content-type' => 'application/json'
            );
        }

        try {
            //Do request and get response with submit data
            $response = self::doRequest('PUT', $serviceurl, $headers, true, true, json_encode($jsonData));
            //request success
            if ($response['status'] == '200' || $response['status'] == '201') {
                if (is_array($response['data']) && isset($response['data']['id'])) {
                    $objectId = $response['data']['id'];
                } else {
                    $error[] = 'error_id_not_found_in_response';
                }

            } else {
                $error = self::parsingErrorFromResponse($response);
            }
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        }

        return $objectId > 0;
    }

    /**
     * Base function for remove record via restful PUT. Return BOOL
     *
     * @param string $serviceurl
     * @param array $headers
     * @param array $error
     * @return boolean
     */
    public static function doDelete($serviceurl = '', $headers = array(), &$error = array())
    {
        $result = false;

        //set default serviceurl from current sdk serviceurl
        if ($serviceurl == '') {
            $serviceurl = static::$serviceurl;
        }

        try {
            //Do request and get response with submit data
            $response = self::doRequest('DELETE', $serviceurl, $headers, true, true);

            //request success
            if ($response['status'] == '200' || $response['status'] == '204') {
                $result = true;

            } else {
                $error = self::parsingErrorFromResponse($response);
            }
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        }

        return $result;
    }

    /**
     * In most case in POST, PUT, DELETE, we need to parse error array from response
     *
     * @param $response
     * @return array
     */
    public static function parsingErrorFromResponse($response)
    {
        $error = array();

        //check valid parsing data
        if (is_array($response['data'])) {
            if (isset($response['data']['error'])) {
                if (is_array($response['data']['error'])) {
                    $error = $response['data']['error'];
                } else {
                    $error[] = $response['data']['error'];
                }
            } else {
                $error[] = 'error_untrack_remote_error';
            }
        } else {
            $error[] = $response['data'];
        }

        return $error;
    }

    /**
     * In some request, we need to customer timeout (make it longer)
     * Use this method to set timeout and with restoreTimeout() method to
     * restore to previous value
     * @param $timeout
     */
    public static function setTmpTimeout($timeout)
    {
        self::$requestTimeoutTmp = self::$requestTimeOut;
        self::$requestTimeOut = $timeout;
    }

    /**
     * Companion method with setTmpTimeout, to restore timeout to saved timeout
     */
    public static function restoreTimeout()
    {
        self::$requestTimeOut = self::$requestTimeoutTmp;
    }

}

