<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 8/22/17
 * Time: 17:46
 */

namespace App\Download;

interface DownloadInterface
{
    /**
     * @return bool
     */
    public function hasFile();

    /**
     * @param $uniqueKey
     * @return mixed
     */
    public function download($uniqueKey);
}