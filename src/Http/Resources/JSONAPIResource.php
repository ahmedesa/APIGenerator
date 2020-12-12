<?php

namespace essa\APIGenerator\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class JSONAPIResource extends JsonResource
{
    use HasRelationship;

    /**
     * @param $request
     * @return array
     * @throws \ReflectionException
     */
    public function toArray($request)
    {
        return [
            $this->merge($this->getResourceIdentifier()),
            'attributes'    => collect($this->allowedAttributes())->merge([
                'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
                'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
                'deleted_at' => $this->deleted_at ? $this->deleted_at->format('d-m-Y H:i:s') : null,
            ]),
            'links'         => [
                'self' => url('/api/' . Str::plural(Str::snake(class_basename($this->resource))) . '/' . $this->id),
            ],
            'relationships' => $this->relationships(),
        ];
    }

    public function getResourceIdentifier()
    {
        return [
            'id'   => $this->id,
            'type' => Str::plural(Str::snake(class_basename($this->resource), '-')),
        ];
    }

    public function allowedAttributes()
    {
        return collect($this->resource->toArray())
            ->except('id', 'created_at', 'updated_at', 'deleted_at')
            ->filter(function($value, $key) {
                return !Str::endsWith($key, '_id');
            })->filter(function($value, $key) {
                return !Arr::has($this->relationships(), $key);
            });
    }

    /**
     * @throws \ReflectionException
     */
    public function relationships()
    {
        $class = get_class($this->resource);

        $reflector = new \ReflectionClass($class);

        $relations = $this->getModelRelations($reflector);

        return $this->prepareRelations($relations);
    }
}
