<?php

namespace Visualbuilder\FilamentUserConsent;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentUserConsentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'filament-user-consent';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([

        ]);

    }

    public function boot(Panel $panel): void
    {
        //
    }
}
