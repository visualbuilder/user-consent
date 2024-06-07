<?php

namespace Visualbuilder\FilamentUserConsent\Resources;

use Filament\Forms\Components\Livewire;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\CreateConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\EditConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\ListConsentOptions;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\RelationManagers\ConsentOptionQuestionsRelationManager;
use Visualbuilder\FilamentUserConsent\Livewire\ConsentOptionPreview;

class ConsentOptionResource extends Resource
{
    protected static ?string $model = ConsentOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    public static function getNavigationLabel(): string
    {
        return config('filament-user-consent.navigation.consent_options.label');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-user-consent.navigation.consent_options.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-user-consent.navigation.consent_options.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-user-consent.navigation.consent_options.sort');
    }

    public static function getCluster(): ?string
    {
        return config('filament-user-consent.navigation.consent_options.cluster');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return config('filament-user-consent.navigation.consent_options.position');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-user-consent.navigation.consent_options.register');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Group::make()->schema([
                        Forms\Components\TextInput::make('title')
                            ->live()
                            ->afterStateUpdated(
                                fn (Set $set, ?string $state) => $set('key', Str::slug($state))
                            )
                            ->required(),
                        Forms\Components\TextInput::make('key')
                            ->required(),
                        Forms\Components\TextInput::make('label')
                            ->hint('(For checkbox)')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->required(),

                        Forms\Components\Toggle::make('enabled')
                            ->label('Enable this contract')
                            ->required(),
                        Forms\Components\Toggle::make('is_survey')
                            ->label('Is this survey consent?')
                            ->required(),
                        Forms\Components\Toggle::make('is_mandatory')
                            ->required(),

                        Forms\Components\Toggle::make('force_user_update')
                            ->label('Require all users to re-confirm after this update')
                            ->required(),

                        Forms\Components\Toggle::make('increment_version')
                            ->label('Do you want to upgrade to next version?')
                            ->required(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->hint('(Will not be active until this date)')
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('models')
                            ->options(config('filament-user-consent.options'))
                            ->multiple()
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('additional_info_title')
                            ->nullable()
                            ->maxLength(150),
                    ])->columns(2)->columnSpanFull(),

                    TiptapEditor::make('text')
                        ->label('Contract text')
                        ->required()
                        ->columnSpanFull(),

                ])->columns(3),
                Section::make('Additional Info')->schema([

                    Repeater::make('fields')->label('')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                        ->regex('/^[a-z_]+$/')
                        ->required(),
                        Forms\Components\Select::make('component')
                            ->options(config('filament-user-consent.components'))
                            ->searchable()
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('label')
                            ->visible(fn(Get $get) => $get('component') !== 'placeholder'),
                        Forms\Components\Toggle::make('required')
                            ->inline(false)
                            ->required(fn(Get $get) => $get('component') !== 'placeholder')
                            ->visible(fn(Get $get) => $get('component') !== 'placeholder'),
                        Forms\Components\RichEditor::make('content')
                            ->required(fn(Get $get) => $get('component') === 'placeholder')
                            ->visible(fn(Get $get) => $get('component') === 'placeholder')
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('options')
                            ->addActionLabel('Add Option')
                            ->keyLabel('Value')
                            ->valueLabel('Label')
                            ->columnSpanFull()
                            ->required(fn(Get $get) => in_array($get('component'), ['select', 'radio', 'likert']))
                            ->visible(fn(Get $get) => in_array($get('component'), ['select', 'radio', 'likert'])),
                    ])
                    ->defaultItems(1)
                    ->columns(2)
                    ->addActionLabel('Add Field')
                    ->collapsed()
                ])->visible(fn(Get $get) => (bool)$get('additional_info'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('version')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_mandatory')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_survey')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_current')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('force_user_update')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usersAcceptedTotal')
                    ->label('Accepted Users'),
                Tables\Columns\TextColumn::make('usersDeclinedTotal')
                    ->label('Declined Users'),
                Tables\Columns\TextColumn::make('published_at')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('Preview')
                ->icon('heroicon-m-viewfinder-circle')
                    ->modalHeading("")
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->form([
                        Livewire::make(ConsentOptionPreview::class),
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ConsentOptionQuestionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsentOptions::route('/'),
            'create' => CreateConsentOption::route('/create'),
            'edit' => EditConsentOption::route('/{record}/edit'),
        ];
    }
}
