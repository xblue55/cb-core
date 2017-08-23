<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 8/22/17
 * Time: 17:32
 */

namespace App\Download;

use CayBua\Constants\Services;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Di;

class UniqueDownload implements DownloadInterface
{
    private $keyExpire;
    private $keyStorage;
    private $keyLength;
    private $prefix;
    private $uniqueKey;
    private $filePath;

    /** @var Redis $redis */
    private $redis;

    /**
     * UniqueDownload constructor.
     * @param string $keyExpire
     * @param string $keyStorage
     * @param int $keyLength
     */
    public function __construct($keyExpire = '+1 month', $keyStorage = 'redis', $keyLength = 1024)
    {
        $this->keyExpire = $keyExpire;
        $this->keyStorage = $keyStorage;
        $this->keyLength = $keyLength;
        $this->prefix = 'unique_download_key_';
        $this->redis = Di::getDefault()->get(Services::REDIS);
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Get uniqueKey
     * @return mixed
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }

    /**
     * Set uniqueKey
     * @param $uniqueKey
     */
    public function setUniqueKey($uniqueKey){
        $this->uniqueKey = $uniqueKey;
    }

    /**
     *  Generate uniqueKey with uniqid
     */
    public function generateUniqueKey()
    {
        $this->setUniqueKey(uniqid('key', TRUE));
        $this->saveUniqueKey();
    }

    /**
     * Save uniqueKey in Redis database
     */
    public function saveUniqueKey()
    {
        if ($this->keyStorage == 'redis') {
            $uniqueKey = $this->generateUniqueKey();
            $this->redis->save($this->prefix, json_encode([
                'key' => $uniqueKey,
                'filePath' => $this->getFilePath(),
                'expire_time' => $this->keyExpire
            ]));
        }
    }

    /**
     * @param $uniqueKey
     * @return bool
     */
    public function deleteUniqueKey($uniqueKey)
    {
        if($this->redis->delete($uniqueKey)){
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function generateDownloadLink()
    {
        return '/download.php?key=' . $this->getUniqueKey();
    }

    /**
     * Check uniqueKey, if key exist, setUniqueKey
     * @param $uniqueKey
     * @return bool
     */
    public function hasUniqueKey($uniqueKey)
    {
        $key = $this->redis->get($this->prefix . $uniqueKey);
        if($key){
            $this->setUniqueKey($key);
        }
        return false;
    }

    /**
     * @param $uniqueKey
     * @return bool
     */
    public function checkExpireTime($uniqueKey)
    {
        return true;
    }

    /**
     * Check file exist
     */
    public function hasFile()
    {
        return is_file($this->getFilePath());
    }

    /**
     * @param $uniqueKey
     * @return bool
     */
    public function download($uniqueKey)
    {
        $isDownload = true;

        if(!$this->hasUniqueKey($uniqueKey)){
            $isDownload = false;
        }
        if(!$this->checkExpireTime($uniqueKey)){
            $isDownload = false;
        }

        if(!$this->$this->hasFile()){
            $isDownload = false;
        }

        return $isDownload;
    }
}