<?php

namespace Visualbuilder\FilamentUserConsent\Resources;

use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\CreateConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\EditConsentOption;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource\Pages\ListConsentOptions;

class ConsentOptionResource extends Resource
{
    protected static ?string $model = ConsentOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function getNavigationGroup(): ?string
    {
        return config('filament-user-consent.navigation.group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-user-consent.navigation.sort');
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
                            ->unique(ignorable: fn ($record) => $record)
                            ->required(),
                        Forms\Components\TextInput::make('label')
                            ->hint('(For checkbox)')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->required(),
                    ])->columns(2)->columnSpanFull(),

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
                            ->required(),
                    ])->columns(2)->columnSpanFull(),

                    Forms\Components\RichEditor::make('text')
                        ->label('Contract text')
                        ->required()
                        ->columnSpanFull(),
                ])->columns(3),
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
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_current')
                    ->sortable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->sortable(),
                Tables\Columns\IconColumn::make('force_user_update')
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
                Tables\Actions\Action::make('Accept')
                    ->requiresConfirmation()
                    ->modalDescription(fn (ConsentOption $record) => $record->label)
                    ->button()
                    ->color('success')
                    ->icon('heroicon-m-hand-thumb-up')
                    ->action(fn (ConsentOption $record) => Notification::make()
                        ->title('Accepted successfully')
                        ->success()
                        ->send()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

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
