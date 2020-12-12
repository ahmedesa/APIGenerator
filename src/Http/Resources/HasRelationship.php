<?php

namespace essa\APIGenerator\Http\Resources;

trait HasRelationship
{
    public function prepareRelations($relations)
    {
        $output = [];
        foreach ($relations as $relation) {
            if (in_array($relation['type'], $this->singleRelations())) {
                $output[$relation['name']] = new JSONAPIResource($this->whenLoaded($relation['name']));
            } elseif (in_array($relation['type'], $this->MultiRelations())) {
                $output[$relation['name']] = new JSONAPICollection($this->whenLoaded($relation['name']));
            }
        }

        return $output;
    }

    public function getRelationClass($relation)
    {
        return $this->withoutNameSpace(get_class($this->resource->{$relation['name']}()->getRelated()));
    }

    public function relationType($relation)
    {
        return $this->withoutNameSpace($relation->getReturnType()->getName());
    }

    public function withoutNameSpace($class)
    {
        $class_parts = explode('\\', $class);

        return end($class_parts);
    }

    public function laravelRelations()
    {
        return array_merge($this->singleRelations(), $this->MultiRelations());
    }

    public function singleRelations()
    {
        return [
            'HasOne', 'BelongsTo', 'MorphTo', 'BelongsToThrough', 'HasOneThrough', 'MorphOne',
        ];
    }

    public function MultiRelations()
    {
        return [
            'HasManyThrough', 'HasMany', 'BelongsToMany', 'MorphToMany',
        ];
    }

    /**
     * @param \ReflectionClass $reflector
     * @return \Illuminate\Support\Collection
     */
    private function getModelRelations(\ReflectionClass $reflector)
    {
        $relations = [];

        foreach ($reflector->getMethods() as $reflectionMethod) {
            $returnType = $reflectionMethod->getReturnType();
            if (!$returnType || !in_array(class_basename($returnType->getName()), $this->laravelRelations())) {
                continue;
            }

            $relations[] = $reflectionMethod;
        }

        return collect($relations)->map(function($relation) {
            return [
                'type' => $this->relationType($relation),
                'name' => $relation->name,
            ];
        });
    }
}