<?php

namespace App\Models\Concerns;

use App\Models\Relations\HasManyInStore;
use App\Models\Relations\HasOneInStore;
use Closure;

trait HasStoreRelations
{
    protected function hasManyInStore(
        string $related,
        string $foreignKey,
        string $localKey,
        string $foreignStoreKey = 'StoreID',
        string $localStoreKey = 'StoreID',
        ?Closure $existenceCondition = null,
    ): HasManyInStore {
        $instance = $this->newRelatedInstance($related);

        return new HasManyInStore(
            $instance->newQuery(), $this, $instance->qualifyColumn($foreignKey), $localKey,
            $foreignStoreKey, $localStoreKey, $existenceCondition,
        );
    }

    protected function hasOneInStore(
        string $related,
        string $foreignKey,
        string $localKey,
        string $foreignStoreKey = 'StoreID',
        string $localStoreKey = 'StoreID',
        ?Closure $existenceCondition = null,
    ): HasOneInStore {
        $instance = $this->newRelatedInstance($related);

        return new HasOneInStore(
            $instance->newQuery(), $this, $instance->qualifyColumn($foreignKey), $localKey,
            $foreignStoreKey, $localStoreKey, $existenceCondition,
        );
    }
}
