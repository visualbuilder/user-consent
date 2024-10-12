<?php

namespace Visualbuilder\FilamentUserConsent\Livewire;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ConsentOptionPreview extends Component implements HasForms
{
    use InteractsWithForms;

    public ConsentOption $consentOption;

    public Model $user;

    public ?array $data = [];

    public function mount(ConsentOption $record)
    {
        $this->consentOption = $record;
        $this->user = auth()->user();
        $this->setDefaultValues();
    }

    public function setDefaultValues()
    {
        $fillData = $this->consentOption->toArray();
        $consentOption = $this->consentOption;
        if($consentOption->questions && $consentOption->questions->count() > 0) {

            foreach ($consentOption->questions as $question) {
                if($question->default_user_column) {
                    $fillData['consents_info'][$consentOption->id][$question->id][$question->name] = $this->user->{$question->default_user_column};
                }

                foreach ($question->additionalInfoOptions as $option) {
                    $optionId = $option->where('id', $option->id)->first();
                    $fillData["consents_info"][$consentOption->id][$question->id][$option->id]["additional_info"] = $this->user->{$optionId->additional_info_default_column};
                }
            }
        }

        $this->form->fill($fillData);
    }

    public function form(Form $form)
    {
        $formInputs = $this->getFormSchema();
        return $form->schema($formInputs)
        ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        $consentOption = $this->consentOption;

        $formFields = [Placeholder::make('welcome')->label('')->content(new HtmlString("Hi {$this->user->firstname},<br>Please read these terms and conditions carefully, we will email a copy to {$this->user->email}"))];

        $fields = [
            Placeholder::make('text')->label('')->content(new HtmlString($consentOption->text)),
            Checkbox::make("consents.$consentOption->id")
            ->label($consentOption->label)
            ->required($consentOption->is_mandatory)
        ];

        if($consentOption->questions && $consentOption->questions->count() > 0) {

            $formComponents = [];
            foreach ($consentOption->questions as $question) {
                $fieldName = "consents_info.$consentOption->id.$question->id.$question->name";
                $options = $question->options ? $question->options->pluck('text', 'id') : [];
                $formComponents[] = match ($question->component) {
                    'placeholder' => Placeholder::make($fieldName)->label('')->content(new HtmlString($question->content))->columnSpanFull(),
                    'likert' => Radio::make($fieldName)->label($question->label ?? '')->options($options)->inline(true)->live()->inlineLabel(false)->required($question->required)->columnSpanFull(),
                    'text' => TextInput::make($fieldName)->label($question->label ?? '')->required($question->required)->columnSpanFull(),
                    'email' => TextInput::make($fieldName)->label($question->label ?? '')->email()->required($question->required)->columnSpanFull(),
                    'select' => Select::make($fieldName)->label($question->label ?? '')->options($options)->live()->required($question->required)->columnSpanFull(),
                    'textarea' => Textarea::make($fieldName)->label($question->label ?? '')->required($question->required)->columnSpanFull(),
                    'number' => TextInput::make($fieldName)->label($question->label ?? '')->numeric()->required($question->required)->columnSpanFull(),
                    'check' => Checkbox::make($fieldName)->label($question->label ?? '')->required($question->required)->columnSpanFull(),
                    'radio' => Radio::make($fieldName)->label($question->label ?? '')->options($options)->live()->columnSpanFull()->required($question->required),
                    'date' => DatePicker::make($fieldName)->label($question->label ?? '')->required($question->required)->columnSpanFull(),
                    'datetime' => DateTimePicker::make($fieldName)->label($question->label ?? '')->required($question->required)->columnSpanFull(),
                };

                if ($question->additionalInfoOptions && $question->additionalInfoOptions->count() > 0 && in_array($question->component, ['radio', 'select', 'likert'])) {
                    foreach ($question->additionalInfoOptions as $option) {
                        $formComponents[] = Textarea::make("consents_info.$consentOption->id.$question->id.$option->id.additional_info")
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
            return $consentOption->is_mandatory ? 'Mandatory' : 'Optional';
        })
        ->icon($consentOption->is_mandatory ? 'heroicon-o-check-badge' : 'heroicon-o-question-mark-circle')
        ->iconColor($consentOption->is_mandatory ? 'success' : 'info')
        ->schema($fields);

        return $formFields;
    }

    public function render()
    {
        return view('user-consent::livewire.consent-option-preview');
    }
}
