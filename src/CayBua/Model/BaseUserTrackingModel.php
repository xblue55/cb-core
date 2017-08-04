<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 8/4/17
 * Time: 16:00
 */

namespace CayBua\Model;

use CayBua\Constants\Services;

abstract class BaseUserTrackingModel extends BaseModel
{
    public $uid;
    public $cid;

    /**
     * @return array
     */
    public function columnMap()
    {
        return parent::columnMap() + [
                'uid' => 'uid',
                'cid' => 'cid'
            ];
    }

    /**
     * User tracking model
     */
    public function beforeValidationOnCreate()
    {
        parent::beforeValidationOnCreate();
        $userService = $this->getDI()->get(Services::USER_SERVICE);
        $userService = $userService->getDetails();
        $this->uid = $userService['id'];
        $this->cid = $userService['cid'];
    }
}