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

    public function getTemperatureLabelAttribute(): string
    {
        if ($this->min_temp === null || $this->max_temp === null) {
            return $this->name;
        }

        return sprintf('%s (%s°C to %s°C)', $this->name, (float) $this->min_temp, (float) $this->max_temp);
    }
}
