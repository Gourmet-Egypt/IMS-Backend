<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'Supplier';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $hidden = ['DBTimeStamp'];

    public function supplierItems(): HasMany
    {
        return $this->hasMany(SupplierList::class, 'SupplierID', 'ID');
    }
}
