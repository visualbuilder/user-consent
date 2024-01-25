<?php

namespace Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages;

use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsentOptions extends ListRecords
{
    protected static string $resource = ConsentOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
