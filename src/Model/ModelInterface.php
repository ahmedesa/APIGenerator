<?php

namespace essa\APIGenerator\Model;

interface ModelInterface
{
    public static function filters();

    public static function includes();

    public static function sorts();
}
