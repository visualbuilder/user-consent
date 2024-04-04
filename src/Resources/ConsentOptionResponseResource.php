<?php

namespace Visualbuilder\FilamentUserConsent\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as ComponentsGrid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Extensions\Nodes\Grid;
use Illuminate\Database\Eloquent\Model;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionUser;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResponseResource\Pages\ListConsentOptionResponses;

class ConsentOptionResponseResource extends Resource
{
    protected static ?string $model = ConsentOptionUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static ?string $label = 'Consent Response';


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
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('consentOption.title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('consentOption.published_at')
                    ->label('Published at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('consentable.full_name')
                    ->label('Username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('consentable_type')
                    ->label('User type')
                    ->formatStateUsing(fn (string $state): string => config('filament-user-consent.options')[$state])
                    ->sortable(),
                Tables\Columns\IconColumn::make('accepted')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Accepted at')
                    ->sortable(),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('consent_option_id')
                    ->label('Consent option')
                    ->searchable()
                    ->options(ConsentOption::pluck('title', 'id')),
                Tables\Filters\SelectFilter::make('consentable_type')
                    ->searchable()
                    ->options(config('filament-user-consent.options'))
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Consent option response')
                    ->schema([
                        TextEntry::make('consentOption.title'),
                        TextEntry::make('consentOption.published_at')->label('Published at'),
                        TextEntry::make('consentable.full_name')->label('Username'),
                        TextEntry::make('consentable_type')
                            ->formatStateUsing(fn (string $state): string => config('filament-user-consent.options')[$state])
                            ->label('User type'),
                        TextEntry::make('created_at')->label('Accepted at'),
                    ])->columns(3),
                Section::make('Quesion & Reponses')
                    ->schema([
                        RepeatableEntry::make('responses')
                            ->schema([
                                ComponentsGrid::make()->schema([
                                    TextEntry::make('question.label')
                                        ->label('Question'),
                                    TextEntry::make('response'),
                                    TextEntry::make('additional_info')
                                        ->visible(fn (Model $model) => $model->additional_info),
                                ])->columns(3)                                
                            ])
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsentOptionResponses::route('/')
        ];
    }
}
