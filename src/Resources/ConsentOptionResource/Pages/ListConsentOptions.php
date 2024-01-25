<?php

namespace Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;

class ListConsentOptions extends ListRecords
{
    protected static string $resource = ConsentOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->hidden(),
        ];
    }
}
