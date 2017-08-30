<?php

namespace CayBua\Data;

class Query extends \PhalconApi\Data\Query
{
    protected $page = null;

    public function hasPage()
    {
        return !is_null($this->page);
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @param Query $query
     * @return $this
     */
    public function mergeQuery($query)
    {
        if ($query->hasFields()) {
            $this->addManyFields($query->getFields());
        }

        if ($query->hasOffset()) {
            $this->setOffset($query->getOffset());
        }

        if ($query->hasLimit()) {
            $this->setLimit($query->getLimit());
        }

        if ($query->hasPage()) {
            $this->setPage($query->getPage());
        }

        if ($query->hasConditions()) {
            $this->addManyConditions($query->getConditions());
        }

        if ($query->hasSorters()) {
            $this->addManySorters($query->getSorters());
        }

        if ($query->hasExcludes()) {
            $this->addManyExcludes($query->getExcludes());
        }

        return $this;
    }
}