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
        $consentOptionModel =  config('filament-user-consent.models.consent_option');
        $this->withCount = $consentOptionModel::getAllUserTypes()
            ->pluck('relation')
            ->toArray();
    }
}
