<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'Configuration';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $guarded = [];
}
