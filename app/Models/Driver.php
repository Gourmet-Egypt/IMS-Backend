<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{

    protected $guarded = [];

    protected $connection = 'sqlsrv_rms';
    protected $table = 'IMS_Drivers';

    protected $casts =
        [
            'is_active' => 'boolean',
        ];
}
