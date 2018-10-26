<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The country that the user belong to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Returns the id of the user after prepending leading zeroes.
     *
     * @param \App\User
     * @return string
     */
    public static function laratablesCustomAction($user)
    {
        return "<a>{$user->id}</a>";
    }

    /**
     * Specify row class name for datatables.
     *
     * @return string
     */
    public function laratablesRowClass()
    {
        return $this->id == 1 ? 'text-success' : 'text-warning';
    }

    /**
     * Returns the data attribute for url to the edit page of the user.
     *
     * @return array
     */
    public function laratablesRowData()
    {
        return [
            'id' => $this->id,
        ];
    }
}
