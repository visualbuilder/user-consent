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
                            <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-200"
                                for="{{ $field['name'] }}">
                                {{ $field['label'] }}
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="text"
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
                                class="peer h-full min-h-[100px] w-full resize-none rounded-[7px]  bg-transparent px-3 py-2.5 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border-t-blue-gray-200 focus:border-2 focus:border-gray-900 focus:border-t-transparent focus:outline-0 disabled:resize-none disabled:border-0 disabled:bg-blue-gray-50"
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
                                        {{ $field['label'] }}
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
</x-dynamic-component>
