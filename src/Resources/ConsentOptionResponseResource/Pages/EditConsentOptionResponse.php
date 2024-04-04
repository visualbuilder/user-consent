<?php

namespace Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResponseResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResponseResource;

class EditConsentOptionResponse extends EditRecord
{
    protected static string $resource = ConsentOptionResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
