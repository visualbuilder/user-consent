<?php

namespace Visualbuilder\FilamentUserConsent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Visualbuilder\FilamentUserConsent\FilamentUserConsent
 */
class FilamentUserConsent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Visualbuilder\FilamentUserConsent\FilamentUserConsent::class;
    }
}
