<?php

namespace App\Models\Relations;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HasOneInStore extends HasOne
{
    use ScopedToStore;

    public function __construct(
        Builder $query,
        Model $parent,
        string $foreignKey,
        string $localKey,
        string $foreignStoreKey = 'StoreID',
        string $localStoreKey = 'StoreID',
        ?Closure $existenceCondition = null,
    ) {
        $this->foreignStoreKey = $foreignStoreKey;
        $this->localStoreKey = $localStoreKey;
        $this->existenceCondition = $existenceCondition;

        parent::__construct($query, $parent, $foreignKey, $localKey);
    }
}
