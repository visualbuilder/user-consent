<?php

namespace Visualbuilder\FilamentUserConsent\Traits;



use Visualbuilder\FilamentUserConsent\Models\ConsentOption;

/**
 * Trait to dynamically inititalise withCount property from user defined
 */
trait UserCount
{
    public function initializeUserCount()
    {
        $this->withCount = ConsentOption::getAllUserTypes()
            ->pluck('relation')
            ->toArray();
    }


}
