<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsType extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'max_temp',
        'min_temp',
        'name',
    ];
    protected $connection = 'sqlsrv_rms';
    protected $table = 'goods_types';
    protected $guarded = [];
}
