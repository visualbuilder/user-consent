<?php

namespace Visualbuilder\FilamentUserConsent\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ConsentOptionRequset extends SimplePage
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    public Model $user;

    public Collection $collection;

    public function mount(): void
    {
        dd(Auth::check());
        $this->user = Auth::user();

        if (! $this->user) {
            abort(403, 'Only authenticated users can set consent options');
        }

        $this->collection = $this->user->outstandingConsents();
        if ($this->collection->count() < 1) {
            abort(403, 'No required consent');
        }
    }

    protected static string $view = 'livewire.consent-option-requset';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->collection)
            ->schema([
                Section::make('Order Number  user')
                    ->schema([
                        TextEntry::make('greetings')->label(''),
                        TextEntry::make('acceptMessage')->label(''),
                    ])
                    ->columnSpan(['lg' => 3]),
            ])->columns(3);
    }
}
