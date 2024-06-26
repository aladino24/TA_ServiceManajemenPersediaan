<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompositeKeyBelongsTo extends BelongsTo
{
    protected $foreignKeys;
    protected $ownerKeys;

    public function __construct(Builder $query, Model $child, array $foreignKeys, array $ownerKeys, $relation)
    {
        $this->foreignKeys = $foreignKeys;
        $this->ownerKeys = $ownerKeys;

        parent::__construct($query, $child, '', '', $relation);
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            foreach ($this->foreignKeys as $index => $foreignKey) {
                $ownerKey = $this->ownerKeys[$index];
                $this->query->where($this->related->getTable().'.'.$ownerKey, '=', $this->child->{$foreignKey});
            }
        }
    }

    public function addEagerConstraints(array $models)
    {
        foreach ($this->foreignKeys as $index => $foreignKey) {
            $keys = collect($models)->pluck($foreignKey)->unique()->values()->all();
            $ownerKey = $this->ownerKeys[$index];

            $this->query->whereIn($this->related->getTable().'.'.$ownerKey, $keys);
        }
    }

    public function getResults()
    {
        foreach ($this->foreignKeys as $index => $foreignKey) {
            if (is_null($this->child->{$foreignKey})) {
                return null;
            }
        }

        return $this->query->first();
    }

    public function getEager()
    {
        return $this->get();
    }
}
