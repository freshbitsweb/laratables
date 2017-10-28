<?php

namespace Freshbitsweb\Laratables;

class Query
{
    protected $query;

    protected $recordsCount;

    protected $filteredCount;

    /**
     * Initialize properties
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    public function __construct($model)
    {
        $this->setQuery($model);
        $this->recordsCount = $this->filteredCount = $this->query->count();
    }

    /**
     * Initialises Query object
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    protected function setQuery($model)
    {
        $this->query = new $model;

        if (method_exists($model, 'datatablesQueryConditions')) {
            $this->query = $model::datatablesQueryConditions($this->query);
        }
    }

    /**
     * Sets the filtered count value after filters are applied
     *
     * @return void
     */
    protected function setFilteredCount()
    {
        $this->filteredCount = $this->query->count();
    }
}
