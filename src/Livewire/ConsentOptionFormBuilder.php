<?php

namespace Visualbuilder\FilamentUserConsent\Livewire;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Get;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Visualbuilder\FilamentUserConsent\Models\ConsentableResponse;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionQuestion;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionQuestionOption;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionUser;
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

        $this->setDefaultValues();
    }

    public function setDefaultValues()
    {
        $fillData = [];
        foreach ($this->user->collections as $key => $consentOption) {
            if($consentOption->questions->count() > 0) {

                foreach ($consentOption->questions as $question) {
                    if($question->default_user_column) {
                        $fillData['consents_info'][$consentOption->id][$question->id][$question->name] = $this->user->{$question->default_user_column};
                    }
                    if($question->additionalInfoOptions && $question->additionalInfoOptions->count() > 0) {
                        foreach ($question->additionalInfoOptions as $option) {
                            $optionId = $option->where('id', $option->id)->first();
                            $fillData["consents_info"][$consentOption->id][$question->id]["additional_info_$option->id"] = $this->user->{$optionId->additional_info_default_column};
                        }
                    }
                }
            }
        }
        $this->form->fill($fillData);
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

            if($consentOption->questions->count() > 0) {

                $formComponents = [];
                foreach ($consentOption->questions as $question) {
                    $fieldName = "consents_info.$consentOption->id.$question->id.$question->name";
                    $options = $question->options;
                    $options = $question->options ? $question->options->pluck('text', 'id') : [];
                    $formComponents[] = match ($question->component) {
                        'placeholder' => Forms\Components\Placeholder::make($fieldName)->label('')->content(new HtmlString($question->content))->columnSpanFull(),
                        'likert' => Forms\Components\Radio::make($fieldName)->label($question->label ?? '')->options($options)->inline(true)->live()->inlineLabel(false)->required($question->required),
                        'text' => Forms\Components\TextInput::make($fieldName)->label($question->label ?? '')->required($question->required),
                        'email' => Forms\Components\TextInput::make($fieldName)->label($question->label ?? '')->email()->required($question->required),
                        'select' => Forms\Components\Select::make($fieldName)->label($question->label ?? '')->options($options)->live()->required($question->required),
                        'textarea' => Forms\Components\Textarea::make($fieldName)->label($question->label ?? '')->required($question->required),
                        'number' => Forms\Components\TextInput::make($fieldName)->label($question->label ?? '')->numeric()->required($question->required),
                        'check' => Forms\Components\Checkbox::make($fieldName)->label($question->label ?? '')->required($question->required),
                        'radio' => Forms\Components\Radio::make($fieldName)->label($question->label ?? '')->options($options)->live()->columnSpanFull()->required($question->required),
                        'date' => Forms\Components\DatePicker::make($fieldName)->label($question->label ?? '')->required($question->required),
                        'datetime' => Forms\Components\DateTimePicker::make($fieldName)->label($question->label ?? '')->required($question->required),
                    };

                    if ($question->additionalInfoOptions && $question->additionalInfoOptions->count() > 0 && in_array($question->component, ['radio', 'select', 'likert'])) {
                        foreach ($question->additionalInfoOptions as $option) {
                            $formComponents[] = Forms\Components\Textarea::make("consents_info.$consentOption->id.$question->id.additional_info_$option->id")
                                ->label($option->additional_info_label ?? 'Additional info')
                                ->visible(fn (Get $get) =>  (int)$get($fieldName) === $option->id)
                                ->required($option->additional_info);
                        }
                    }
                }
                $fields[] = Section::make($consentOption->additional_info_title)->schema($formComponents)->columns(3);
            }

            $formFields[] = Section::make("{$consentOption->title}")
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
        $consentInfo = $formData['consents_info']??[];

        $conentIds = [];
        foreach($formData['consents'] as $key => $value) {
            if((bool)$value === true) {
                $conentIds[] = $key;
            }
        }

        $outstandingConsents = $this->user->outstandingConsents();
        foreach ($outstandingConsents as $consentOption) {
            $consentOption = $consentOption->refresh();
            $this->user->consents()
                ->save(
                    $consentOption,
                    [
                        'accepted' => in_array($consentOption->id, $conentIds),
                        'key' => $consentOption->key,
                    ]
                );

            if($consentOption->questions->count() > 0) {
                $consentable = $this->user->consents()->where('consent_option_id', $consentOption->id)->first();
                $consentable = $consentable->pivot;
                $consentable = ConsentOptionUser::where('consentable_type', $consentable->consentable_type)
                    ->where('consentable_id', $consentable->consentable_id)
                    ->where('consent_option_id', $consentable->consent_option_id)
                    ->where('accepted', $consentable->accepted)
                    ->first();
                foreach ($consentInfo[$consentOption->id] as $id => $question) {
                    $key = array_keys($question);
                    $fieldName = $key[0];
                    $questionModel = ConsentOptionQuestion::find($id);
                    $questionOptionModel = ConsentOptionQuestionOption::find($question[$fieldName]);
                    $additionalInfoColumn = "additional_info_".$question[$fieldName];
                    $additionalInfoValue = "";
                    if(isset($question[$additionalInfoColumn]) && $questionOptionModel) {
                        $additionalInfoValue = $question[$additionalInfoColumn];
                    }

                    ConsentableResponse::create([
                        'consentable_id' => $consentable->id,
                        'consent_option_id' => $consentOption->id,
                        'consent_option_question_id' => $id,
                        'consent_option_question_option_id' => $questionOptionModel?->id,
                        'question_field_name' => $fieldName,
                        'response' => $questionOptionModel ? $questionOptionModel->value : $question[$fieldName],
                        'additional_info' => $additionalInfoValue,
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Success')
            ->body('Your consent preferences have been saved.')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->send();

        $this->user->notify(new ConsentsUpdatedNotification());

        $this->redirect(request()->session()->get('url.saved'));
    }
}
