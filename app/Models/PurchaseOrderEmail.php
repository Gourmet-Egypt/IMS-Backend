<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderEmail extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_rms';

    protected $table = 'purchase_order_emails';

    protected $guarded = [];

    /**
     * Scope to get emails for specific stores OR emails marked to receive all
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $storeIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStores($query, array $storeIds)
    {
        return $query->where('is_active', '1')
            ->whereIn('store_id', $storeIds)
            ->where('receive_all', '0');
    }
}
