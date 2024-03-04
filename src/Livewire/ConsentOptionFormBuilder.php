<?php

namespace Visualbuilder\FilamentUserConsent\Livewire;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Visualbuilder\FilamentUserConsent\Notifications\ConsentsUpdatedNotification;

class ConsentOptionFormBuilder extends SimplePage implements Forms\Contracts\HasForms
{
    use InteractsWithForms, InteractsWithFormActions;

    public static ?string $title = 'Your consent is required';

    protected static string $view = 'user-consent::livewire.consent-option-form-builder';

    public Model $user;

    public array $consents = [];

    public array $consents_info = [];

    public static function getSort(): int
    {
        return -1;
    }

    public static function canView()
    {
        return false;
    }

    public function getMaxWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }

    public function mount(): void
    {
        $this->user = auth()->user();

        if(!$this->user) {
            abort(403, 'Forbidden User');
        }

        $this->user->collections = $this->user->outstandingConsents();

        if ($this->user->collections->count() < 1) {
            abort(403, 'No required consent');
        }
    }


    protected function getFormSchema(): array
    {
        if(!$this->user->collections) {
            $this->user->collections = $this->user->outstandingConsents();
        }
        $formFields = [Forms\Components\Placeholder::make('welcome')->label('')->content(new HtmlString("Hi {$this->user->firstname},<br>Please read these terms and conditions carefully, we will email a copy to {$this->user->email}"))];
        foreach($this->user->collections as $consentOption){
            $fields = [
                Forms\Components\Placeholder::make('text')->label('')->content(new HtmlString($consentOption->text)),
                Forms\Components\Checkbox::make("consents.$consentOption->id")
                ->label($consentOption->label)
                ->required($consentOption->is_mandatory)
            ];

            if((int)$consentOption->additional_info === 1) {

                $additionInfo = [];
                foreach ($consentOption->fields as $field) {
                    $fieldName = "consents_info.$consentOption->id.{$field['name']}";
                    $fieldLabel = $field['label'] ?? '';
                    $columnSpan = $field['column_span'] ?? 1;
                    $options = array_combine(explode(',',$field['options']), explode(',',$field['options']));
                    

                    $additionInfo[] = match ($field['type']) {
                        'text' => $this->prepareField(Forms\Components\TextInput::make($fieldName)->label($fieldLabel)->columnSpan($columnSpan), $field, $consentOption),
                        'email' => $this->prepareField(Forms\Components\TextInput::make($fieldName)->label($fieldLabel)->email()->columnSpan($columnSpan), $field, $consentOption),
                        'select' => $this->prepareField(Forms\Components\Select::make($fieldName)->label($fieldLabel)->options($options)->columnSpan($columnSpan), $field, $consentOption),
                        'textarea' => $this->prepareField(Forms\Components\Textarea::make($fieldName)->label($fieldLabel)->columnSpan($columnSpan), $field, $consentOption),
                        'number' => $this->prepareField(Forms\Components\TextInput::make($fieldName)->label($fieldLabel)->numeric()->columnSpan($columnSpan), $field, $consentOption),
                        'check' => $this->prepareField(Forms\Components\Checkbox::make($fieldName)->label($fieldLabel)->columnSpan($columnSpan), $field, $consentOption),
                        'radio' => $this->prepareField(Forms\Components\Radio::make($fieldName)->label($fieldLabel)->options($options)->columnSpan($columnSpan), $field, $consentOption),
                        'date' => $this->prepareField(Forms\Components\DatePicker::make($fieldName)->label($fieldLabel)->columnSpan($columnSpan), $field, $consentOption),
                        'datetime' => $this->prepareField(Forms\Components\DateTimePicker::make($fieldName)->label($fieldLabel)->columnSpan($columnSpan), $field, $consentOption),
                    };
                }

                $fields[] = Section::make($consentOption->additional_info_title)->schema($additionInfo)->columns(3);
            }

            $formFields[] = Section::make("{$consentOption->title} v{$consentOption->version}")
                ->description(function () use($consentOption) {
                    $suffix = $this->previousConsents($consentOption->key);
                    $mandatory = $consentOption->is_mandatory ? 'Mandatory' : 'Optional';
                    if ($suffix) {
                        $mandatory .= " - ( $suffix )";
                    }
                    return $mandatory;
                })
                ->icon($consentOption->is_mandatory ? 'heroicon-o-check-badge' : 'heroicon-o-question-mark-circle')
                ->iconColor($consentOption->is_mandatory ? 'success' : 'info')
                ->schema($fields);
        }

        return $formFields;
    }

    public function previousConsents($key)
    {
        if ($this->user->hasPreviousConsents($key)) {
            $lastViewed = $this->user->lastConsentByKey($key);
            return 'Our consent statement has been updated since you last ' . $lastViewed->pivot->accepted ? 'accepted' : 'viewed' . ' ' . $lastViewed->pivot->created_at->diffForHumans();
        }
    }

    public function submit(): void
    {
        $formData = $this->form->getState();
        
        $conentIds = [];
        foreach($formData['consents'] as $key => $value) {
            if((bool)$value === true) {
                $conentIds[] = $key;
            }
        }

        $outstandingConsents = $this->user->outstandingConsents();
        foreach ($outstandingConsents as $consentOption) {
            $this->user->consents()
                ->save(
                    $consentOption,
                    [
                        'accepted' => in_array($consentOption->id, $conentIds),
                        'key' => $consentOption->key,
                        'fields' => ((bool)$consentOption->additional_info && isset($formData['consents_info'])) ? $formData['consents_info'][$consentOption->id] : []
                    ]
                );
        }

        Notification::make()
            ->title('Success')
            ->body('Your consent preferences have been saved.')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->send();

        // $this->user->notify(new ConsentsUpdatedNotification());
        
        if(Session::has('url.saved')) {
            $this->redirect(request()->session()->get('url.saved'));
        }
    } 

    public function prepareField($component, $fieldOption, $consentOption)
    {
        if(isset($fieldOption['custom_rules']) && (bool)$fieldOption['custom_rules']) {

            foreach ($fieldOption['rules'] as $key => $value) {
            
                if($value['rule_type'] == "require_if_another_field") {
                    $anotherField = "consents_info.$consentOption->id.{$value['another_field']}";   
                    $component->requiredIf($anotherField, $fieldOption['value_equals']);
                }  
            }
        }
        return $component->required((bool)$fieldOption['required']);
    }
}
