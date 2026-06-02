<x-filament-panels::page.simple>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        @php($consentCount = $this->user->outstandingConsents()->count())
        <div class="mt-3 text-end">
            {{-- Single button: Filament swaps the icon for a spinner in place while
                 submitting, so the button keeps its size instead of jumping to a
                 separate, wider "Submitting…" button. --}}
            <x-filament::button
                type="submit"
                icon="heroicon-m-sparkles"
                wire:target="submit"
                wire:loading.attr="disabled"
            >
                {{ $consentCount === 1 ? 'Submit Consent' : 'Submit Consents' }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page.simple>
