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
     * @return mixed
     */
    public function download();
}