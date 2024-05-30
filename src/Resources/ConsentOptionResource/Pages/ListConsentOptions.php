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
            Actions\CreateAction::make(),
        ];
    }

    public function getHeading(): string
    {
        return config('filament-user-consent.navigation.consent_options.page_heading');
    }
}
