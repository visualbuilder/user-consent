<?php

namespace Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResponseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResponseResource;

class ListConsentOptionResponses extends ListRecords
{
    protected static string $resource = ConsentOptionResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getHeading(): string
    {
        return config('filament-user-consent.navigation.consent_responses.page_heading');
    }
}
