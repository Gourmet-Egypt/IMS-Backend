<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_rms';
    protected $hidden = ['DBTimeStamp'];

    protected $table = 'Store';
    protected $guarded = [];
}
