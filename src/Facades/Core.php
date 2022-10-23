<?php

namespace Novacio\Core\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Novacio\Core\Core\Core
 */
class Core extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Novacio\Core\Core\Core::class;
    }
}
