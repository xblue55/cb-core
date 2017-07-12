<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/26/17
 * Time: 16:54
 */

namespace CayBua\Model;

use CayBua\Constants\Services;
use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
    public $id;
    public $ipaddress;
    public $datecreated;
    public $datemodified;

    public function columnMap()
    {
        return [
            'ipaddress' => 'ipaddress',
            'datecreated' => 'datecreated',
            'datemodified' => 'datemodified',
        ];
    }

    public function beforeValidationOnCreate()
    {
        $this->datecreated = time();
        $this->datemodified = $this->datecreated;
        $request = $this->getDI()->get(Services::REQUEST);
        $this->ipaddress = ip2long($request->getClientAddress());
    }

    public function beforeUpdate()
    {
        $this->datemodified = time();
        $request = $this->getDI()->get(Services::REQUEST);
        $this->ipaddress = ip2long($request->getClientAddress());
    }
}