<?php

namespace App\Support\Traits;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use InvalidArgumentException;

trait IsSorted
{
    public static function bootIsSorted()
    {
        static::creating(function($model){
            if ($model->shouldSortWhenCreating()) {
                $model->setHighestOrderNumber();
            }
        });
    }

    public function setHighestOrderNumber(): void
    {
        $orderColumnName = $this->determineOrderColumnName();

        $this->$orderColumnName = $this->getHighestOrderNumber() + 1;
    }

    public function getHighestOrderNumber(): int
    {
        return (int) $this->buildSortQuery()->max($this->determineOrderColumnName());
    }

    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy($this->determineOrderColumnName(), $direction);
    }

    /*
     * This function reorders the records: the record with the first id in the array
     * will get order 1, the record with the second it will get order 2, ...
     *
     * A starting order number can be optionally supplied.
     */
    public static function setNewOrder($ids, int $startOrder = 1, string $primaryKeyColumn = null)
    {
        if (! is_array($ids) && ! $ids instanceof ArrayAccess) {
            throw new InvalidArgumentException('You must pass an array or ArrayAccess object to setNewOrder');
        }

        $model = new static;

        $orderColumnName = $model->determineOrderColumnName();

        if (is_null($primaryKeyColumn)) {
            $primaryKeyColumn = $model->getKeyName();
        }

        foreach ($ids as $id) {
            static::withoutGlobalScope(SoftDeletingScope::class)
                ->where($primaryKeyColumn, $id)
                ->update([$orderColumnName => $startOrder++]);
        }
    }

    public static function setNewOrderByCustomColumn(string $primaryKeyColumn, $ids, int $startOrder = 1)
    {
        self::setNewOrder($ids, $startOrder, $primaryKeyColumn);
    }

    protected function determineOrderColumnName(): string
    {
        return $this->sortable['order_column_name'] ?? 'order_column';
    }

    public function shouldSortWhenCreating(): bool
    {
        return $this->sortable['sort_when_creating'] ?? true;
    }

    public function buildSortQuery()
    {
        return static::query();
    }
}
