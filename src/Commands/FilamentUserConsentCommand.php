<?php

namespace visualbuilder\FilamentUserConsent\Commands;

use Illuminate\Console\Command;

class FilamentUserConsentCommand extends Command
{
    public $signature = 'filament-user-consent';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
