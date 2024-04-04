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
}
