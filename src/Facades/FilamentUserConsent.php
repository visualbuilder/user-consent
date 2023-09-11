<?php

namespace visualbuilder\FilamentUserConsent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \visualbuilder\FilamentUserConsent\FilamentUserConsent
 */
class FilamentUserConsent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \visualbuilder\FilamentUserConsent\FilamentUserConsent::class;
    }
}
