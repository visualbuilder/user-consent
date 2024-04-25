<x-filament-panels::page.simple>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-3 text-end">
            <x-filament::button type="submit" wire:loading>
                <div class="flex"><x-filament::loading-indicator class="h-5 w-5 mx-3"/> Submitting consents...</div>
            </x-filament::button>
            <x-filament::button icon="heroicon-m-sparkles" type="submit" wire:loading.remove>
                Submit Consents
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page.simple>
