<?php

namespace Visualbuilder\FilamentUserConsent\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;

class ConsentOptionRequset extends SimplePage
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    public Model $user;

    public Collection $collection;

    public function mount(): void
    {
        $this->user = Auth::guard('admin')->user();

        if (! $this->user) {
            abort(403, 'Only authenticated users can set consent options');
        }

        $this->user->collections = $this->user->outstandingConsents();
        if ($this->user->collections->count() < 1) {
            abort(403, 'No required consent');
        }
    }

    protected static string $view = 'vendor.user-consent.livewire.consent-option-requset';

    public function getMaxWidth(): MaxWidth | string | null
    {
        return MaxWidth::SixExtraLarge;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->user)
            ->schema([
                Section::make('User Info')
                    ->schema([
                        TextEntry::make('fullName'),
                        TextEntry::make('email'),
                    ])
                    ->columns(2),
                RepeatableEntry::make('collections')
                    ->label('Your Consent is required')
                    ->schema([
                        Section::make(fn (ConsentOption $record) => "{$record->title} V{$record->version}")
                            ->description(function (ConsentOption $record) {
                                $suffix = $this->previousConsents($record->key);
                                $mandatory = $record->is_mandatory ? 'Mandatory' : 'Optional';
                                if ($suffix) {
                                    $mandatory .= " - ( $suffix )";
                                }

                                return $mandatory;
                            })
                            ->icon(fn (ConsentOption $record) => $record->is_mandatory ? 'heroicon-o-check-badge' : 'heroicon-o-question-mark-circle')
                            ->iconColor(fn (ConsentOption $record) => $record->is_mandatory ? 'success' : 'info')
                            ->schema([
                                TextEntry::make('text')->label('')
                                    ->markdown(),
                                TextEntry::make('updated_at')->label('Last Updated'),
                                Actions::make([
                                    Action::make('acceptConsent')
                                        ->label(fn (ConsentOption $record) => $record->label)
                                        ->icon('heroicon-o-check-circle')
                                        ->color(fn (ConsentOption $record) => $record->is_mandatory ? 'success' : 'info')
                                        ->action(function (array $data, ConsentOption $record) {

                                            $this->user->consents()
                                                ->save(
                                                    $record,
                                                    [
                                                        'accepted' => $record->id,
                                                        'key' => $record->key,
                                                    ]
                                                );
                                            Notification::make()
                                                ->title($record->title)
                                                ->body('Consent accepted successfully')
                                                ->icon('heroicon-o-check-circle')
                                                ->color('success')
                                                ->send();
                                            // event(new ConsentUpdated($consentOption, $request->consent_option[$consentOption->id]));
                                            // event(new ConsentsUpdatedComplete($outstandingConsents, $user));
                                        }),
                                ]),
                            ]),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])->columns(3);
    }

    public function previousConsents($key)
    {
        $user = Auth::guard('admin')->user();
        if ($user->hasPreviousConsents($key)) {
            $lastViewed = $user->lastConsentByKey($key);

            return 'Our consent statement has been updated since you last ' . $lastViewed->pivot->accepted ? 'accepted' : 'viewed' . ' ' . $lastViewed->pivot->created_at->diffForHumans();
        }
    }
}
