<form wire:submit.prevent="submit">
    {{ $this->form }}

    <div class="mt-3">
        <x-filament::button icon="heroicon-m-sparkles" type="submit">
            Submit Consents
        </x-filament::button>
    </div>
</form>
