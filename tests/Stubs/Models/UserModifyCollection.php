<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserModifyCollection extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Set user emails as the name on the collection.
     *
     * @param \Illuminate\Support\Collection
     * @param \Illuminate\Support\Collection
     */
    public static function laratablesModifyCollection($users)
    {
        return $users->map(function ($user) {
            $user->name = $user->email;

            return $user;
        });
    }
}
