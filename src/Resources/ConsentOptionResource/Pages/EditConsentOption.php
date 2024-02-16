<?php

namespace Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ((bool)$data['increment_version']) { //$record->usersViewedThisVersion
            //create a new version
            $data['version'] = $record->nextVersionNumber;
            $data['key'] = $record->key;
            $record = ConsentOption::create($data);
        } else {
            $record->update($data);
        }
        if ($record->canPublish) {
            $record->setCurrentVersion();
        }

        return $record;
    }
}
