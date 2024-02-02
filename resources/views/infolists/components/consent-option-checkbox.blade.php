<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <label>
        <x-filament::input.checkbox value="{{$getRecord()->id}}" wire:model="acceptConsents" />
        <span class="mx-3">
            {{ $getRecord()->label }}
        </span>
    </label>
</x-dynamic-component>
