<?php

namespace CayBua\Di;

use CayBua\Constants\Services;
use CayBua\QueryParsers\PhqlQueryParser;

use PhalconApi\Constants\ErrorCodes;
use PhalconApi\Exception;

class FactoryDefault extends \PhalconApi\Di\FactoryDefault
{
    public function __construct()
    {
        parent::__construct();

        $this->setShared(Services::FRACTAL_MANAGER, function () {

            $className = '\League\Fractal\Manager';

            if (!class_exists($className)) {
                throw new Exception(ErrorCodes::GENERAL_SYSTEM, null,
                    '\League\Fractal\Manager was requested, but class could not be found');
            }

            return new $className();
        });

        $this->setShared(Services::PHQL_QUERY_PARSER, new PhqlQueryParser);
    }
}