<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <label>
        <x-filament::input.checkbox value="{{ $getRecord()->id }}"
            wire:model="acceptConsents.{{ $getRecord()->id }}.accept" />
        <span class="mx-3">
            {{ $getRecord()->label }}
        </span>
    </label>

    <div class="my-4"></div>

    @if ($getRecord()->additional_info && ($fields = $getRecord()->fields))
        <x-filament::section>
            <x-slot name="heading">
                Additional Info
            </x-slot>
            <div class="grid grid-cols-3 gap-4">
                @foreach ($fields as $key => $field)
                    @if ($field['type'] == 'text')
                        <div class="mb-4">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200 @if ((bool) $field['required'] === true) required @endif"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="text"
                                    wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}" />
                            </x-filament::input.wrapper>
                            {{-- @if ($errorsBag[$getRecord()->id][$field['name']]) <span class="error">{{ $errorsBag[$getRecord()->id][$field['name']] }}</span> @endif --}}
                        </div>
                    @elseif($field['type'] == 'email')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="email"
                                    wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}" />
                            </x-filament::input.wrapper>
                        </div>
                    @elseif($field['type'] == 'email')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="email"
                                    wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}" />
                            </x-filament::input.wrapper>
                        </div>
                    @elseif($field['type'] == 'select')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input.select
                                    wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}">
                                    <option value="">--Select--</option>
                                    @foreach (explode(',', $field['options']) as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </x-filament::input.select>
                            </x-filament::input.wrapper>
                        </div>
                    @elseif($field['type'] == 'textarea')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <textarea
                                class="bg-gray-200 appearance-none rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white bg-white/5 border-gray-300  dark:border-white/10 dark:bg-white/5"
                                wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}">
                                </textarea>
                        </div>
                    @elseif($field['type'] == 'number')
                        <div class="my-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}">
                                </x-filament::input>
                            </x-filament::input.wrapper>
                        </div>
                    @elseif($field['type'] == 'check')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <label>
                                <x-filament::input.checkbox value="1"
                                    wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}" />
                                <span class="mx-3">
                                    {{ $field['label'] }}
                                </span>
                            </label>
                        </div>
                    @elseif($field['type'] == 'radio')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            @foreach (explode(',', $field['options']) as $option)
                                <label>
                                    <input type="radio" value="{{ $option }}"
                                        wire:model="acceptConsents.{{ $getRecord()->id }}.{{ $field['name'] }}" />
                                    <span class="mx-3">
                                        {{ $option }}
                                    </span>
                                </label>
                            @endforeach
                            </label>
                        </div>
                    @endif
                @endforeach
            </div>
        </x-filament::section>
    @endif

    <x-filament::button wire:click="submit">
        New user
    </x-filament::button>

</x-dynamic-component>
