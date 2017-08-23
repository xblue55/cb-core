<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 8/22/17
 * Time: 17:32
 */

namespace CayBua\Download;

use CayBua\Constants\Services;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Di;

class UniqueDownload implements DownloadInterface
{
    private $keyExpireTime;
    private $keyStorage;
    private $keyLength;
    private $prefix;
    private $uniqueKey;
    private $filePath;
    private $downloadLimited;

    /** @var Redis $redis */
    private $redis;

    /**
     * UniqueDownload constructor.
     * @param string $keyExpireTime
     * @param string $keyStorage
     * @param int $keyLength
     * @param int $downloadLimited
     * @internal param string $keyExpire
     */
    public function __construct($keyExpireTime = '+1 month', $keyStorage = 'redis', $keyLength = 1024, $downloadLimited = 5)
    {
        $this->keyExpireTime = $keyExpireTime;
        $this->keyStorage = $keyStorage;
        $this->keyLength = $keyLength;
        $this->prefix = 'unique_download_key_';
        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->downloadLimited = $downloadLimited;
    }


    /**
     * @return string
     */
    public function getKeyExpireTime()
    {
        return $this->keyExpireTime;
    }

    /**
     * @param string $keyExpireTime
     */
    public function setKeyExpireTime($keyExpireTime)
    {
        $this->keyExpireTime = $keyExpireTime;
    }

    /**
     * @return int
     */
    public function getDownloadLimited()
    {
        return $this->downloadLimited;
    }

    /**
     * @param int $downloadLimited
     */
    public function setDownloadLimited($downloadLimited)
    {
        $this->downloadLimited = $downloadLimited;
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
        $this->setUniqueKey(uniqid());
    }

    /**
     * Save uniqueKey in Redis database
     */
    public function saveUniqueKey()
    {
        if ($this->keyStorage == 'redis') {
            $this->redis->save($this->prefix.$this->getUniqueKey(), [
                'key' => $this->getUniqueKey(),
                'file_path' => $this->getFilePath(),
                'expire_time' => $this->keyExpireTime,
                'download_limited' => $this->downloadLimited
            ]);
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