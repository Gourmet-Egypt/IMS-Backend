<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    use HasFactory;

    protected $table = 'Item';
    protected $guarded = [];

    public function infos()
    {
        return $this->hasMany(TransferredItemInfo::class) ;
    }


    public function transferRequests(): HasMany
    {
        return $this->hasMany(TransferRequestItem::class);
    }

    public function transfers():BelongsToMany
    {
        return $this->belongsToMany(TransferRequest::class, 'transfer_request_item')
            ->withPivot('quantity', 'unit_price', 'notes')
            ->withTimestamps();
    }


    public function scopeSearch(Builder $query, $lookupcode) :void
    {
        $query->where('ItemLookupCode', $lookupcode);
    }

}
