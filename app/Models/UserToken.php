<?php


namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;

class UserToken extends PersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    /**
     * Get the tokenable model that the access token belongs to.
     */
    public function tokenable()
    {
        return $this->morphTo('tokenable');
    }
}
