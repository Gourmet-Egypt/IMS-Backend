<?php

namespace App\Models\Relations;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait ScopedToStore
{
    protected string $foreignStoreKey;

    protected string $localStoreKey;

    protected ?Closure $existenceCondition;

    public function addConstraints()
    {
        if (static::$constraints) {
            parent::addConstraints();

            $this->getRelationQuery()->where(
                $this->related->qualifyColumn($this->foreignStoreKey),
                $this->parent->getAttribute($this->localStoreKey),
            );
        }
    }

    public function addEagerConstraints(array $models)
    {
        $this->getRelationQuery()
            ->whereIn($this->foreignKey, $this->getKeys($models, $this->localKey))
            ->whereIn(
                $this->related->qualifyColumn($this->foreignStoreKey),
                $this->getKeys($models, $this->localStoreKey),
            );
    }

    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
    {
        $dictionary = $results->mapToDictionary(fn ($result) => [
            $this->storeKey($result->getAttribute($this->foreignStoreKey), $result->getAttribute($this->getForeignKeyName())) => $result,
        ])->all();

        foreach ($models as $model) {
            $key = $this->storeKey($model->getAttribute($this->localStoreKey), $model->getAttribute($this->localKey));

            if (isset($dictionary[$key])) {
                $model->setRelation($relation, $this->getRelationValue($dictionary, $key, $type));
            }
        }

        return $models;
    }

    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($this->existenceCondition === null) {
            $query = parent::getRelationExistenceQuery($query, $parentQuery, $columns);
        } else {
            if ($query->getQuery()->from == $parentQuery->getQuery()->from) {
                $hash = $this->getRelationCountHash();
                $query->from($query->getModel()->getTable() . ' as ' . $hash);
                $query->getModel()->setTable($hash);
            }

            $query->select($columns)->whereRaw(
                ($this->existenceCondition)($query->getModel()->getTable(), $this->parent->getTable())
            );
        }

        return $query->whereColumn(
            $query->getModel()->qualifyColumn($this->foreignStoreKey),
            '=',
            $this->parent->qualifyColumn($this->localStoreKey),
        );
    }

    private function storeKey($store, $key): string
    {
        return (int) $store . '|' . trim((string) $key);
    }
}
