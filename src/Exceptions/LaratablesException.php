<?php

namespace Freshbitsweb\Laratables\Exceptions;

use Exception;

class LaratablesException extends Exception
{
    private $error;

    public function __construct($error)
    {
        $this->error = $error;
    }

    /**
     * Returns the error to datatables when exception is thrown.
     *
     * @return array
     */
    public function render()
    {
        return [
            'error' => $this->error,
        ];
    }
}
