<?php

namespace essa\APIGenerator\Repositories;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseRepository
{
    public function getModelName(): string
    {
        return Str::plural(Str::snake(class_basename($this->model), '-'));
    }

    public function fetchAll()
    {
        return QueryBuilder::for($this->model)
                           ->allowedFilters(config("jsonapi.resources." . $this->getModelName() . ".allowedFilters"))
                           ->allowedIncludes(config("jsonapi.resources." . $this->getModelName() . ".allowedIncludes"))
                           ->allowedSorts(config("jsonapi.resources." . $this->getModelName() . ".allowedSorts"))
                           ->get();
    }
}