<?php

namespace essa\APIGenerator\Generator;

use Illuminate\Support\Str;

trait FileManger
{
    protected function getTemplate($type)
    {
        return str_replace(
            [
                'Dummy',
                'Dummies',
                'dummy',
                'dummies',
            ],
            [
                $this->model,
                Str::plural($this->model),
                lcfirst($this->model),
                lcfirst(Str::plural($this->model)),
            ],
            $this->getStubs($type, $this->with_image)
        );
    }

    protected function getStubs($type, $with_image)
    {
        $location = 'default';
        if ($with_image) {
            $location = 'with_image';
        }

        return file_get_contents(__DIR__ . '/../Stubs/' . $location . '/' . $type . ".stub");
    }
}