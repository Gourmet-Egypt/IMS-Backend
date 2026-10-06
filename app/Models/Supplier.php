<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

    public static function allItemsSuppliers(): Collection
    {
        $names = config('suppliers.all_items');

        return collect(Cache::remember('suppliers.all_items', now()->addHour(), fn () => static::query()
            ->whereIn('Code', array_map('strval', array_keys($names)))
            ->get(['ID', 'Code'])
            ->mapWithKeys(fn ($s) => [(int) $s->ID => $names[$s->Code] ?? $s->Code])
            ->all()));
    }

    public static function allItemsIds(): Collection
    {
        return static::allItemsSuppliers()->keys();
    }

    public function hasAllItems(): bool
    {
        return static::allItemsIds()->contains((int) $this->ID);
    }
}
