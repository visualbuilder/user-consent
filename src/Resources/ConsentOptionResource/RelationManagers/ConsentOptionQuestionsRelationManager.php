<?php

namespace Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\RelationManagers;

use App\Models\SurveyQuestionType;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Support\Str;

class ConsentOptionQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\Select::make('component')
                        ->required()
                        ->options(config('filament-user-consent.components'))
                        ->live()
                        ->searchable(),
                    Forms\Components\TextInput::make('name')
                        ->required(fn(Get $get) => $get('component') !== 'placeholder')
                        ->regex('/^[a-z_]+$/')
                        ->live(true)
                        ->hint('Alpha & underscore only allowed. (Ex: some_name)')
                        ->maxLength(191)
                        //->formatStateUsing(fn (string $state): string => Str::snake($state))
                        ->afterStateUpdated(function ($state, $set) {
                            $snake = Str::snake($state);
                            // Update the name field's state to the snake case version of the input
                            $set('name', $snake);
                        }),
                    Forms\Components\TextInput::make('label')
                        ->nullable()
                        ->maxLength(255),
                    Forms\Components\Select::make('default_user_column')
                        ->options(config('filament-user-consent.autofill_columns'))
                        ->searchable(),
                    Forms\Components\TextInput::make('sort')
                        ->required()
                        ->numeric(),               
                    Forms\Components\Toggle::make('required')
                        ->inlineLabel(false)
                        ->inline(false)
                        ->visible(fn(Get $get) => $get('component') !== 'placeholder'),
                ])->columns(3),
                Section::make()->schema([
                    Repeater::make('options')
                        ->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('value')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('text')
                                ->required(),                            
                            Forms\Components\TextInput::make('additional_info_label')
                                ->label('Additional info label')                                
                                ->required(fn(Get $get) => $get('additional_info')),
                            Forms\Components\Select::make('additional_info_default_column')
                                ->options(config('filament-user-consent.autofill_columns'))
                                ->searchable(),
                            Forms\Components\Toggle::make('additional_info')
                                ->label('Additional info required?')
                                ->inline(true)
                                ->live()
                                ->required(),
                        ])
                        ->reorderable(true)
                        ->orderColumn('sort')
                        ->columns(2)
                ])->visible(fn (Get $get) => in_array($get('component'), ['likert', 'select', 'radio', 'check'])),
                Section::make()->schema([
                    TiptapEditor::make('content')
                        ->label("HTML Content")
                        ->tools([])
                        ->profile('default')
                        ->columnSpanFull()
                ])->visible(fn (Get $get) => in_array($get('component'), ['placeholder']))

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort')
            ->columns([
                Tables\Columns\TextColumn::make('component'),
                Tables\Columns\TextColumn::make('label')->words(5),
                Tables\Columns\TextColumn::make('sort'),
                Tables\Columns\IconColumn::make('required'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
