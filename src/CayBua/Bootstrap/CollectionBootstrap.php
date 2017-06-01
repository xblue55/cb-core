<?php

namespace CayBua\Bootstrap;

use CayBua\BootstrapInterface;
use CayBua\Collections\ExportCollection;
use App\Model\ProductCategory;
use App\Resources\ProductResouce;
use App\Resources\UserResource;
use App\Resources\ProductCategoryResource;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

class CollectionBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api
            ->collection(new ExportCollection('/export'))
            ->resource(new ProductResouce('/products'))
            ->resource(new ProductCategoryResource('/productcategories'));
    }
}
