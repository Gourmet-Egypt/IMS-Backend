<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    use HasFactory;

    protected $table = 'Cashier';
    protected $guarded = [];
    

    public function scopeSearch(Builder $query, $store_id)
    {
        return $query->where([
            'StoreID' => $store_id,
            'SecurityLevel' => 4
        ]);

    }
}
