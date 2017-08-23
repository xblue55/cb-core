<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 8/22/17
 * Time: 17:32
 */

namespace App\Download;

class UniqueDownload implements DownloadInterface
{
    private $keyExpire;
    private $keyStorage;
    private $keyLength;

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
    }

    /**
     * @return string
     */
    public function writeUniqueKey()
    {
        $key = uniqid('key', TRUE);
        return $key;
    }

    /**
     * @return bool
     */
    public function hasUniqueKey()
    {
        return true;
    }

    /**
     * @param $key
     * @return bool
     */
    public function deleteUniqueKey($key)
    {
        return true;
    }

    /**
     * @return string
     */
    public function generateDownloadLink()
    {
        return '';
    }

    public function hasFile()
    {

    }

    public function download()
    {

    }
}