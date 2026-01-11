<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemperatureRange extends Model
{
    protected $guarded = [];

//    protected $table = 'IMS_Temperature_ranges';

    protected $connection = 'sqlsrv_rms';

    use HasFactory;
}
