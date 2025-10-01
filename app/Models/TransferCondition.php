<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferCondition extends Model
{
    use HasFactory;

    protected $table = 'transfer_conditions' ;

    protected $guarded = [] ;


    public function transferRequest(): BelongsTo
    {
        return $this->belongsTo(TransferRequest::class);
    }
}
