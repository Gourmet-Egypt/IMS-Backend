<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;


//    protected $table = 'IMS_Vehicle_types';

    protected $connection = 'sqlsrv_rms';

    protected $guarded = [];
}
