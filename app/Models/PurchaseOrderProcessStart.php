<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProcessStart extends Model
{
    protected $table = 'purchase_order_process_starts';

    protected $fillable = [
        'purchase_order_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function startLabel(): ?string
    {
        return $this->start_date?->copy()->setTimezone('Africa/Cairo')->format('d/m/Y H:i');
    }

    public function endLabel(): ?string
    {
        return $this->end_date?->copy()->setTimezone('Africa/Cairo')->format('d/m/Y H:i');
    }

    public function leadTimeLabel(): ?string
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return CarbonInterval::seconds($this->start_date->diffInSeconds($this->end_date, true))
            ->cascade()
            ->forHumans(['short' => true]);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'ID');
    }
}
