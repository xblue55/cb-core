<?php

namespace CayBua\User;

use CayBua\Constants\AclRoles;

use CayBua\Constants\ConfigConstants;
use CayBua\Constants\HeaderConstants;
use CayBua\Constants\Services;
use CayBua\Http\UserHttp;
use Phalcon\Di;
use Phalcon\Mvc\Model;
use PhalconApi\User\Service as PhalconApiService;

class Service extends PhalconApiService {
    /**
     * @return string
     */
    public function getRole() {
        $userModel = $this->getDetails();
        
        $role = AclRoles::UNAUTHORIZED;
        $headers = $this->request->getHeaders();
        /**
         * Check Headers has accessTrustedKey and accessTrustedKey match the accessTrustedKey at config
         * This is private service request
         * AclRoles is a LOCAL_SERVICE
         */
        $key = HeaderConstants::ACCESS_TRUSTED_KEY;
        if (empty($userModel) && array_key_exists($key, $headers)) {
            $config = Di::getDefault()->get(Services::CONFIG);
            $accessTrustedKey = $config->get(ConfigConstants::ACCESS_TRUSTED_KEY);
            if (!empty($headers[$key]) && ($accessTrustedKey === $headers[$key])) {
                $role = AclRoles::LOCAL_SERVICE;
            }
        }
        if (is_object($userModel)) {
            /** @var Model $userModel */
            $userModel = $userModel->toArray();
        }
        if (!empty($userModel) && in_array(ucfirst(strtolower($userModel['role'])), AclRoles::ALL_ROLES)) {
            $role = ucfirst(strtolower($userModel['role']));
        }
        
        return $role;
    }
    
    /**
     * @param mixed $identity
     *
     * @return array
     */
    protected function getDetailsForIdentity($identity) {
        $details = [];
        $token = $this->authManager->getSession()->getToken();
        $userHttp = new UserHttp();
        $myUser = $userHttp->getUserInformationWithToken($token);
        if (isset($myUser['data']['item']) && $myUser['data']['item']['id'] > 0) {
            $details = $myUser['data']['item'];
        }
        
        return $details;
    }
    
    /**
     * @return mixed
     */
    public function getTickets() {
        $user = $this->getDetails();
        
        return $user['tickets'];
    }
    
    /**
     * @param $method
     * @param $uri
     *
     * @return bool
     */
    public function allowRbacPermission($method, $uri) {
        $userRole = $this->getRole();
        if ($userRole == AclRoles::ADMINISTRATOR) {
            return true;
        }
        $tickets = $this->getTickets();
        $uri = preg_replace('/[0-9]+/', '{id}', $uri);
        foreach ($tickets as $ticket) {
            if (($ticket['method'] == $method) && ($ticket['slug'] == $uri)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * get company id
     *
     * @return mixed
     */
    public function getCompanyOfCurrentUserLogin() {
        $token = $this->authManager->getSession()->getToken();
        $redis = Di::getDefault()->get(Services::REDIS);
        $companyId = (int)$redis->get($token);
        if ($companyId < 1) {
            $companyId = -1;
        }
        
        return $companyId;
    }
    
    /**
     * get user id login
     *
     * @return mixed
     */
    public function getUserId() {
        $userModel = $this->getDetails();
        
        return $userModel['id'];
    }
}