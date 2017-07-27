<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/26/17
 * Time: 16:54
 */

namespace CayBua\Model;

use CayBua\Constants\Services;
use Phalcon\Di;
use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
    public $id;
    public $ipaddress;
    public $datecreated;
    public $datemodified;

    /**
     * @return array
     */
    public function columnMap()
    {
        return [
            'id' => 'id',
            'ipaddress' => 'ipaddress',
            'datecreated' => 'datecreated',
            'datemodified' => 'datemodified',
        ];
    }

    /**
     * Add tracking date and IP address when create model
     */
    public function beforeValidationOnCreate()
    {
        $this->datecreated = time();
        $this->datemodified = $this->datecreated;
        $request = $this->getDI()->get(Services::REQUEST);
        $this->ipaddress = ip2long($request->getClientAddress());
    }

    /**
     * Add tracking date and IP address when update model
     */
    public function beforeUpdate()
    {
        $this->datemodified = time();
        $request = $this->getDI()->get(Services::REQUEST);
        $this->ipaddress = ip2long($request->getClientAddress());
    }

    /**
     * @return array
     */
    public function getMessagesArray(){
        $messages = $this->getMessages();
        $messagesResponse = [];
        /** @var \Phalcon\Mvc\Model\Message $message */
        foreach ($messages as $message) {
            $messagesResponse[] = $message->getMessage();
        }
        return $messagesResponse;
    }

    /**
     * @param $resourceServerNumber
     * @param $path
     * @return string
     */
    public function getImageFullPath($resourceServerNumber, $path)
    {
        $config = Di::getDefault()->get(Services::CONFIG);
        $resourceServers = $config->get('resourceServer');
        $fullPath = '';
        foreach ($resourceServers as $key => $resourceServerPath) {
            if ($key == $resourceServerNumber) {
                $fullPath = $resourceServerPath . $path;
            }
        }
        return $fullPath;
    }

    /**
     * @param $resourceServerPath
     * @return int|string
     */
    public function setImageResourceServer($resourceServerPath){
        $config = Di::getDefault()->get(Services::CONFIG);
        $resourceServers = $config->get('resourceServer');
        $resourceServerNumber = 0;
        foreach ($resourceServers as $key => $resourceServerPathConfig) {
            if ($resourceServerPathConfig == $resourceServerPath) {
                $resourceServerNumber = $key;
            }
        }
        return $resourceServerNumber;
    }
}