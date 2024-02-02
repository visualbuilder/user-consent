<?php

namespace Visualbuilder\FilamentUserConsent\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
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

    public $acceptConsents = [];

    public function mount(): void
    {
        if (Auth::guard('admin')->check()) {
            $this->user = Auth::guard('admin')->user();
        } elseif (Auth::guard('enduser')->check()) {
            $this->user = Auth::guard('enduser')->user();
        } elseif (Auth::guard('practitioner')->check()) {
            $this->user = Auth::guard('practitioner')->user();
        }

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

    public static function getSort()
    {
        return 0;
    }

    public static function canView()
    {
        return false;
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
                                Group::make()->schema([
                                    ViewEntry::make('acceptConsent')
                                        ->label('')
                                        ->view('vendor.user-consent.infolists.components.consent-option-checkbox'),
                                    TextEntry::make('updated_at')->label('Last Updated'),
                                ])->columns(2),
                            ]),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Actions::make([
                    Action::make('saveConsents')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (array $data) {
                            $this->acceptConsent();
                        }),
                ]),
            ])->columns(3);
    }

    public function previousConsents($key)
    {
        if ($this->user->hasPreviousConsents($key)) {
            $lastViewed = $this->user->lastConsentByKey($key);

            return 'Our consent statement has been updated since you last ' . $lastViewed->pivot->accepted ? 'accepted' : 'viewed' . ' ' . $lastViewed->pivot->created_at->diffForHumans();
        }
    }

    public function acceptConsent()
    {
        $validateMandatoryConsents = $this->user->requiredOutstandingConsentsValidate($this->acceptConsents);

        if (! $validateMandatoryConsents) {
            Notification::make()
                ->title('Please confirm.!')
                ->body('Please accept all required consent options.')
                ->icon('heroicon-o-check-circle')
                ->color('danger')
                ->send();
        } else {

            $outstandingConsents = $this->user->outstandingConsents();
            foreach ($outstandingConsents as $consentOption) {
                $this->user->consents()
                    ->save(
                        $consentOption,
                        [
                            'accepted' => in_array($consentOption->id, $this->acceptConsents),
                            'key' => $consentOption->key,
                        ]
                    );
                // event(new ConsentUpdated($consentOption, $request->consent_option[ $consentOption->id ]));
            }
            Notification::make()
                ->title('Welcome.!')
                ->body('Your submitted all consent options are saved.')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->send();

            // event(new ConsentsUpdatedComplete($outstandingConsents, $user));

            // return Redirect::intended(
            //     $request->session()
            //         ->get('url.saved')
            // );
        }
    }
}
