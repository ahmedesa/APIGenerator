<?php

namespace essa\APIGenerator\Model;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
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
        return !is_null($this->image) ? asset('storage/' . $this->image) : null;
    }
}
