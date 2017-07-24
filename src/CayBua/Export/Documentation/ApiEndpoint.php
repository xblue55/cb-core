<?php

/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/13/17
 * Time: 23:49
 */
namespace CayBua\Export\Documentation;

class ApiEndpoint extends \PhalconRest\Export\Documentation\ApiEndpoint
{
    protected $exampleParameters;
    protected $exampleHeaders;

    /**
     * @return mixed
     */
    public function getExampleParameters()
    {
        return $this->exampleParameters;
    }

    /**
     * @param mixed $exampleParameters
     */
    public function setExampleParameters($exampleParameters)
    {
        $this->exampleParameters = $exampleParameters;
    }

    /**
     * @return mixed
     */
    public function getExampleHeaders()
    {
        return $this->exampleHeaders;
    }

    /**
     * @param mixed $exampleHeaders
     */
    public function setExampleHeaders($exampleHeaders)
    {
        $this->exampleHeaders = $exampleHeaders;
    }
}