<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsType extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_rms';

//    protected $table = 'IMS_Goods_types';

    protected $guarded = [];
}
