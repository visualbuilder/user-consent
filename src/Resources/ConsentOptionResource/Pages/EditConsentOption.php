<?php

namespace Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;

class EditConsentOption extends EditRecord
{
    protected static string $resource = ConsentOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->hidden(),
        ];
    }

    public function getTitle(): string
    {
        return "Edit {$this->record->title} version {$this->record->version}";
    }
}
