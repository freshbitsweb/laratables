<?php

namespace Freshbitsweb\Laratables;

class Laratables
{
    public $foo;

    public function __construct()
    {
        $this->foo = 'Bar';
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param type var Description
     * @return return type
     */
    public function getFoo()
    {
        return $this->foo;
    }
}
