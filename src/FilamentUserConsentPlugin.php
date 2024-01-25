<?php

namespace Visualbuilder\FilamentUserConsent;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Visualbuilder\FilamentUserConsent\Resources\ConsentOptionResource;

class FilamentUserConsentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-user-consent';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ConsentOptionResource::class,
        ]);

    }

    public function boot(Panel $panel): void
    {
        //
    }
}
