<?php

namespace Livewire\Features\SupportTesting;

use Illuminate\Support\MessageBag;
use Livewire\ComponentHook;

/**
 * Patched version of Livewire's SupportTesting that handles null error bags.
 * This override is only loaded during tests to fix compatibility issues.
 *
 * Loaded via composer classmap in autoload-dev.
 */
class SupportTesting extends ComponentHook
{
    function dehydrate($context)
    {
        $target = $this->component;

        $errors = $target->getErrorBag();

        if ($errors === null || !($errors instanceof \Illuminate\Contracts\Support\MessageBag)) {
            $errors = new MessageBag();
        }

        if (! $errors->isEmpty()) {
            $this->storeSet('testing.errors', $errors);
        }
    }
}
