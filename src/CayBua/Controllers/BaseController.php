<?php

/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/28/17
 * Time: 13:22
 */

namespace CayBua\Controllers;

use CayBua\Mvc\Controllers\CrudResourceController;

abstract class BaseController extends CrudResourceController
{

    public static function whoAmI()
    {
        return get_called_class();
    }
}