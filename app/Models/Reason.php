<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;


//    protected $table = 'IMS_Reasons';
    protected $connection = 'sqlsrv_rms';


    protected $guarded = [];
}
