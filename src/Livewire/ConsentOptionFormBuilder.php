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
                    $options = array_combine(explode(',',$field['options']), explode(',',$field['options']));
                    $additionInfo[] = match ($field['type']) {
                        'text' => Forms\Components\TextInput::make($fieldName)->label($field['label'])->required((bool)$field['required']),
                        'email' => Forms\Components\TextInput::make($fieldName)->label($field['label'])->email()->required((bool)$field['required']),
                        'select' => Forms\Components\Select::make($fieldName)->label($field['label'])->options($options)->required((bool)$field['required']),
                        'textarea' => Forms\Components\Textarea::make($fieldName)->label($field['label'])->required((bool)$field['required']),
                        'number' => Forms\Components\TextInput::make($fieldName)->label($field['label'])->numeric()->required((bool)$field['required']),
                        'check' => Forms\Components\Checkbox::make($fieldName)->label($field['label'])->required((bool)$field['required']),
                        'radio' => Forms\Components\Radio::make($fieldName)->label($field['label'])->options($options)->required((bool)$field['required']),
                        'date' => Forms\Components\DatePicker::make($fieldName)->label($field['label'])->required((bool)$field['required']),
                        'datetime' => Forms\Components\DateTimePicker::make($fieldName)->label($field['label'])->required((bool)$field['required']),
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
        $consentInfo = $formData['consents_info'];
        
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
                        'fields' => (bool)$consentOption->additional_info ? $consentInfo[$consentOption->id] : []
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

        $this->redirect(request()->session()->get('url.saved'));
    } 
}