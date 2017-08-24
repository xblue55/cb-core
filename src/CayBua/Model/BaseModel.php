<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/26/17
 * Time: 16:54
 */

namespace CayBua\Model;

use CayBua\Constants\ConfigConstants;
use CayBua\Constants\Services;
use CayBua\Http\UserHttp;
use Phalcon\Mvc\Model;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;

abstract class BaseModel extends Model
{
    public $id;
    public $ipaddress;
    public $datecreated;
    public $datemodified;

    public function whoAmI()
    {
        return get_called_class();
    }

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
    public function getMessagesArray()
    {
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
     * @return string
     */
    public function getImageResourceServer($resourceServerNumber)
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $resourceServers = $config->get(ConfigConstants::RESOURCE_SERVER);
        $resourceServerPath = '';
        foreach ($resourceServers as $key => $resourceServerPathConfig) {
            if ($key == $resourceServerNumber) {
                $resourceServerPath = $resourceServerPathConfig;
            }
        }
        return $resourceServerPath;
    }

    /**
     * @param $resourceServerPath
     * @return int|string
     */
    public function setImageResourceServer($resourceServerPath)
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $resourceServers = $config->get(ConfigConstants::RESOURCE_SERVER);
        $resourceServerNumber = 0;
        foreach ($resourceServers as $key => $resourceServerPathConfig) {
            if ($resourceServerPathConfig == $resourceServerPath) {
                $resourceServerNumber = $key;
            }
        }
        return $resourceServerNumber;
    }

    /**
     * @param $userID
     * @return array
     */
    public function getUser($userID)
    {
        $userHttp = new UserHttp();
        $userProfileDataResponse = $userHttp->getUseProfileWithUserID($userID);
        $userProfileData = $userProfileDataResponse['data']['item'];
        if (isset($userProfileData) && !empty($userProfileData)) {
            return $userProfileData;
        }
        return [];
    }

    /**
     * @param $multiData
     * @param bool $ignore
     * @return mixed
     */
    public function batchInsert($multiData, $ignore = false)
    {
        /** @var Database $database */
        $database = $this->getDI()->get(Services::DB);
        if ($ignore) {
            $insertString = /** @lang SQL text */
                "INSERT IGNORE INTO %s (%s) VALUES %s";
        } else {
            $insertString = /** @lang SQL text */
                "INSERT INTO %s (%s) VALUES %s";
        }
        $modelName = $this->whoAmI();
        /** @var BaseModel $model */
        $model = new $modelName();
        $sql = sprintf(
            $insertString,
            $this->getSource(),
            $this->setRows($model->toArray()),
            $this->setValues($multiData)
        );
        $execute = $database->execute($sql);
        if ($execute === false) {
            return false;
        } else {
            return $execute;
        }
    }

    /**
     * @param $key
     * @return string
     */
    private function setRows($key)
    {
        return sprintf('%s', implode(',', array_keys($key)));
    }

    /**
     * @param $multiData
     * @return string
     */
    private function setValues($multiData)
    {
        $insertString = '';
        $modelName = $this->whoAmI();
        foreach ($multiData as $data) {
            /** @var BaseModel $model */
            $model = new $modelName($data);
            $model->beforeValidationOnCreate();
            $dataProcessValue = $model->toArray();
            $sql = '';
            foreach ($dataProcessValue as $value) {
                $sql .= isset($value) ? ("'" . $value . "',") : "NULL,";
            }
            $insertString .= '(' . substr($sql, 0, -1) . '),';
        }
        $insertString = substr($insertString, 0, -1);
        return $insertString;
    }
}