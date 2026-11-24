<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemDynamic extends Model
{
    protected $connection = 'sqlsrv_rms';

    protected $table = 'ItemDynamic';

    protected $hidden = ['DBTimeStamp'];

    public $timestamps = false;
}
