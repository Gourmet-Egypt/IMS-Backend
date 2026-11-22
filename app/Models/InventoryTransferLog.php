<?php

namespace App\Models;

use App\Models\Concerns\HasStoreRelations;
use App\Models\Relations\HasOneInStore;
use Illuminate\Database\Eloquent\Model;

class InventoryTransferLog extends Model
{
    use HasStoreRelations;

    protected $connection = 'sqlsrv_rms';

    protected $table = 'InventoryTransferLog';

    protected $hidden = ['DBTimeStamp'];

    public $timestamps = false;

    public const TYPE_PURCHASE_ORDER = 1;

    public function cashier(): HasOneInStore
    {
        return $this->hasOneInStore(Cashier::class, 'ID', 'CashierID');
    }
}
