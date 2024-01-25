<?php

namespace Visualbuilder\FilamentUserConsent\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\CreateConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\EditConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\ListConsentOptions;

class ConsentOptionResource extends Resource
{
    protected static ?string $model = ConsentOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Forms\Components\TextInput::make('title')
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
                    Forms\Components\Toggle::make('is_mandatory')
                        ->required(),
                    Forms\Components\Toggle::make('force_user_update')
                        ->label('Require all users to re-confirm after this update')
                        ->required(),
                    Group::make()->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->hint('(Will not be active until this date)')
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('models')
                            ->options(config('filament-user-consent.options'))
                            ->multiple()
                            ->searchable()
                            ->required()
                    ])->columns(2)->columnSpanFull(),
                    Forms\Components\RichEditor::make('text')
                        ->label('Contract text')
                        ->required()
                        ->columnSpanFull()
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key'),
                Tables\Columns\TextColumn::make('version'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\IconColumn::make('is_mandatory'),
                Tables\Columns\IconColumn::make('is_current'),
                Tables\Columns\IconColumn::make('enabled'),
                Tables\Columns\IconColumn::make('force_user_update'),
                Tables\Columns\TextColumn::make('published_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
