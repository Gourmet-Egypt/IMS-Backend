<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierList extends Model
{
    use HasFactory;

    protected $table = 'SupplierList';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $hidden = ['DBTimeStamp'];

        protected $casts = [
        'ID' => 'integer',
        'ItemID' => 'integer',
        'SupplierID' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'ItemID', 'ID');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'ID');
    }
}
