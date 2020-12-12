<?php

namespace essa\APIGenerator\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class JSONAPICollection extends ResourceCollection
{
    public $collects = JSONAPIResource::class;

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'count' => $this->collection->count(),
            'data'  => $this->collection,
        ];
    }
}
