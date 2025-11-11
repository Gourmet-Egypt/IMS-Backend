<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'Department' ;
    protected $connection = 'sqlsrv_rms';

    protected $hidden = ['DBTimeStamp'];
}
