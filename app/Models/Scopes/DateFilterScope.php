<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DateFilterScope implements Scope
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function apply(Builder $builder, Model $model): void
    {
        if (!empty($this->filters['date_from'])) {
            $builder->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $builder->whereDate('created_at', '<=', $this->filters['date_to']);
        }
    }
}