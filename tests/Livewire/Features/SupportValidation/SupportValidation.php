<?php

namespace Livewire\Features\SupportValidation;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\ComponentHook;
use Livewire\Drawer\Utils;

/**
 * Patched version of Livewire's SupportValidation that handles null error bags.
 * This override is only loaded during tests to fix compatibility issues.
 *
 * Loaded via composer classmap in autoload-dev.
 */
class SupportValidation extends ComponentHook
{
    function hydrate($memo)
    {
        $this->component->setErrorBag($memo['errors'] ?? []);
    }

    function render($view, $data)
    {
        $errorBag = $this->component->getErrorBag();

        if ($errorBag === null || !($errorBag instanceof \Illuminate\Contracts\Support\MessageBag)) {
            $errorBag = new MessageBag();
            $this->component->setErrorBag($errorBag);
        }

        $errors = (new ViewErrorBag)->put('default', $errorBag);

        $revert = Utils::shareWithViews('errors', $errors);

        return function () use ($revert) {
            $revert();
        };
    }
}
