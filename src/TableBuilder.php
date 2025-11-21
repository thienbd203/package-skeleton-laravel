<?php

namespace InertiaKit\InertiaTableBuilder;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

abstract class TableBuilder
{
    protected QueryBuilder $query;

    public function __construct(protected Request $request)
    {
        $this->query = QueryBuilder::for($this->getBaseQuery());
        $this->applyRequest();
    }

    abstract protected function getBaseQuery(): Builder;

    abstract protected function columns(): array;

    protected function filters(): array
    {
        return [];
    }

    protected function sorts(): array
    {
        return [];
    }

    protected function actions(): array
    {
        return [];
    }

    protected function applyRequest(): void
    {
        if ($filters = $this->filters()) {
            $this->query->allowedFilters($filters);
        }

        if ($sorts = $this->sorts()) {
            $this->query->allowedSorts($sorts);
        }
    }

    public function toJson(): array
    {
        return [
            'columns' => $this->columns(),
            'filters' => $this->filters(),
            'sorts' => $this->sorts(),
            'actions' => $this->actions(),
            'rows' => $this->query->get()->toArray(),
        ];
    }
}
