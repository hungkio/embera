<?php

namespace App\Support\Traits;

use Astrotomic\Translatable\Translatable as BaseTranslatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

trait Translatable
{
    use BaseTranslatable;

    public function scopeOrderByTranslation(Builder $query, string $translationField, string $sortMethod = 'asc')
    {
        $translationTable = $this->getTranslationsTable();
        $localeKey = $this->getLocaleKey();
        $table = $this->getTable();
        $keyName = $this->getKeyName();

        return $query
            ->select("{$table}.*")
            ->leftJoin($translationTable, function (JoinClause $join) use ($translationTable, $localeKey, $table, $keyName) {
                $join
                    ->on("{$translationTable}.{$this->getTranslationRelationKey()}", '=', "{$table}.{$keyName}")
                    ->where("{$translationTable}.{$localeKey}", $this->locale());
            })
            ->orderBy("{$translationTable}.{$translationField}", $sortMethod);
    }
}
