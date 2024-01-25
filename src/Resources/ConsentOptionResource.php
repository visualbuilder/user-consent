<?php

namespace Visualbuilder\FilamentUserConsent\Resources;

use App\Filament\Resources\ConsentOptionResource\RelationManagers;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                //
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
