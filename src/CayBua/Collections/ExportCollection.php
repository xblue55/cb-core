<?php

namespace CayBua\Collections;

use CayBua\Controllers\ExportController;
use CayBua\Api\ApiCollection;
use CayBua\Api\ApiEndpoint;

class ExportCollection extends ApiCollection
{
    protected function initialize()
    {
        $this
            ->name('Export')
            ->handler(ExportController::class)
            ->endpoint(ApiEndpoint::get('/documentation.json', 'documentation'))
            ->endpoint(ApiEndpoint::get('/postman.json', 'postman'));
    }
}