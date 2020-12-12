<?php

namespace essa\APIGenerator\Model;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model implements ModelInterface
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * get image full link
     * @param $image
     * @return ?string
     */
    public function getImage($image)
    {
        if (!is_null($this->$image)) {
            return asset('storage/' . $this->$image);
        }

        return null;
    }
}
