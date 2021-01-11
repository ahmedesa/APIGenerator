<?php

namespace essa\APIGenerator\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class JSONAPIResource extends JsonResource
{
    use HasRelationship;

    private $self_link;

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
                'self' => $this->getSelfLink(),
            ],
            'relationships' => empty($this->resource->getRelations()) ? (object)[] : $this->relationships(),
        ];
    }

    public function getSelfLink()
    {
        if ($this->self_link) {
            return url((str_replace('{id}', $this->id, $this->self_link)));
        }

        $model_name = Str::plural(Str::snake(class_basename($this->resource)));
        $config = config("jsonapi.resources." . $model_name . ".link");

        $link = isset($config)
            ? (str_replace('{id}', $this->id, $config))
            : '/api/' . $model_name . '/' . $this->id;

        return url($link);
    }

    /**
     * @param mixed $self_link
     * @return JSONAPIResource
     */
    public function setSelfLink($self_link): JSONAPIResource
    {
        $this->self_link = $self_link;

        return $this;
    }

    public function getResourceIdentifier(): array
    {
        return [
            'id'   => $this->id,
            'type' => Str::plural(Str::snake(class_basename($this->resource), '-')),
        ];
    }

    public function allowedAttributes(): ?\Illuminate\Support\Collection
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
    public function relationships(): ?array
    {
        $class = get_class($this->resource);

        $reflector = new \ReflectionClass($class);

        $relations = $this->getModelRelations($reflector);

        return $this->prepareRelations($relations);
    }
}
