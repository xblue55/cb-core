<?php

namespace CayBua\Mvc;

class DateTrackingModel extends BaseModel
{
    public function beforeCreate()
    {
        $this->datecreated = time();
        $this->datemodified = $this->datecreated;
    }

    public function beforeUpdate()
    {
        $this->datemodified = time();
    }
}
