<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProcessStart extends Model
{
    protected $table = 'purchase_order_process_starts';

    protected $fillable = [
        'purchase_order_id',
        'start_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'ID');
    }
}
