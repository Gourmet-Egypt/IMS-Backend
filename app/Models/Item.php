<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $table = 'Item';
    protected $guarded = [];

    protected $hidden = ['DBTimeStamp'];

    public static function onSecondary()
    {
        return static::on('sqlsrv_rms');
    }


    public function transferRequests(): HasMany
    {
        return $this->hasMany(TransferRequestItem::class);
    }

    public function transfers(): BelongsToMany
    {
        return $this->belongsToMany(TransferRequest::class, 'transfer_request_item')
            ->withPivot('quantity', 'unit_price', 'notes')
            ->withTimestamps();
    }


    public function scopeShowSearch(Builder $query, $lookupcode): void
    {
        $query->where('ItemLookupCode', 'like', "{$lookupcode}%");
    }

    public function scopeIndexSearch(Builder $query, $last_updated = null): void
    {

        $query
            ->where([
                ['DoNotOrder', 0]
                , ['Inactive', 0],
                ['HQID', '<>', 0]

            ])
            ->when($last_updated, function ($q) use ($last_updated) {
                $q->where('LastUpdated', '>', $last_updated);
            });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'CategoryID', 'ID');
    }


    public function department()
    {
        return $this->belongsTo(Department::class, 'DepartmentID', 'ID');
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(Alias::class, 'ItemID', 'ID');
    }


}
