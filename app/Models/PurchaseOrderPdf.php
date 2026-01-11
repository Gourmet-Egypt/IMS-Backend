<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class PurchaseOrderPdf extends Model
{
    use HasFactory, Prunable;

    protected $table = 'purchase_order_pdfs';

    protected $guarded = [];


}
