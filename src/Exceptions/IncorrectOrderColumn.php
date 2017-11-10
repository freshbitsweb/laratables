<?php

namespace Freshbitsweb\Laratables\Exceptions;

class IncorrectOrderColumn extends LaratablesException
{
    public static function name($columnName)
    {
        return new static("The '$columnName' column is not in the select column list. Please add it to the select columns or make it - orderable : false");
    }
}
