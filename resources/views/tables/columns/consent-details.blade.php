@php
    use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;
    $model = $getRecord();
@endphp
<x-filament::section>
    <x-slot name="heading">
        {{ $model->title }}
    </x-slot>
    <x-slot name="headerEnd">
        @if ($model->isHighestVersion)
            <x-filament::button color="warning" title="{{ __('Edit') }} {{ $model }}"
                icon="heroicon-m-pencil-square" href="{{ ConsentOptionResource::getUrl('edit', ['record' => $model]) }}"
                tag="a" class="mx-2">
                {{ __('Edit') }}
            </x-filament::button>
        @else
            <x-filament::button color="danger" title="{{ __('Edit') }} {{ $model }}"
                icon="heroicon-m-lock-closed"></x-filament::button>
        @endif

        @if ($model->is_active)
            <x-filament::button color="success" title="Active" icon="heroicon-m-check-circle">
                Active
            </x-filament::button>
        @else
            @if ($model->is_current)
                <x-filament::button color="danger" title="Disabled" icon="heroicon-m-exclamation-circle">
                    Disabled
                </x-filament::button>
            @else
                <x-filament::button color="info" title="{{ $this->isHighestVersion ? 'draft' : 'locked' }}"
                    icon="heroicon-m-information-circle">
                    {{ $this->isHighestVersion ? 'draft' : 'locked' }}
                </x-filament::button>
            @endif
        @endif
    </x-slot>
    <div class="fi-section-content-ctn border-gray-200 dark:border-white/10">
        <div class="fi-section-content p-2">
            {!! $model->text !!}

            <div class="flex mt-6">
                @if ($model->published_at->lt(\Illuminate\Support\Carbon::now()))
                    <x-filament::badge color="info">
                        <b>{{ __('Published On') }}</b>:
                        {{ $model->published_at->format('jS M Y') }}
                    </x-filament::badge>
                @else
                    <x-filament::badge color="danger">
                        <b>{{ __('Will be automatically published on') }}</b>:
                        {{ $model->published_at->format('jS M Y') }}
                    </x-filament::badge>
                @endif
            </div>
        </div>
    </div>
    <div class="fi-secion-footer w-full px-6 pb-6">
        <div class="fi-secion-footer-actions gap-3 flex flex-wrap items-center">
            <div class="me-auto mb-0">
                @if ($model->is_mandatory)
                    <x-filament::badge color="success" icon="heroicon-m-check-badge">Mandatory</x-filament::badge>
                @else
                    <x-filament::badge color="warning" icon="heroicon-m-question-mark-circle">
                        Optional</x-filament::badge>
                @endif
                for
                @foreach ($model->models as $user)
                    <x-filament::badge color="info"
                        icon='heroicon-m-user-circle'>{{ $model::modelBasename($user) }}</x-filament::badge>
                @endforeach
            </div>
            <div class="ms-auto mb-0">
                <x-filament::badge color="success" icon="heroicon-m-hand-thumb-up">Accepted
                    {{ $model->usersAcceptedTotal }} </x-filament::badge>
                @if ($model->is_mandatory)
                    <x-filament::badge color="danger" icon="heroicon-m-hand-thumb-down">Declined
                        {{ $model->usersDeclinedTotal }}</x-filament::badge>
                @endif
            </div>
        </div>
    </div>
</x-filament::section>
