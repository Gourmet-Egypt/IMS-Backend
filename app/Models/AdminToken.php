<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;

class AdminToken extends PersonalAccessToken
{
    protected $connection = 'sqlsrv_rms';
    protected $table = 'personal_access_tokens';

    /**
     * Get the tokenable model that the access token belongs to.
     */
    public function tokenable()
    {
        return $this->morphTo('tokenable');
    }
}
