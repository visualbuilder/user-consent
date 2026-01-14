<?php

namespace Visualbuilder\FilamentUserConsent\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConsentOptionFactory extends Factory
{
    protected $model = \Visualbuilder\FilamentUserConsent\Models\ConsentOption::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true);

        return [
            'key'               => Str::slug($title),
            'version'           => $this->faker->randomNumber(),
            'title'             => $title,
            'label'             => 'Tick here to accept the terms',
            'text'              => $this->faker->paragraph,
            'is_survey'         => $this->faker->boolean(),
            'additional_info_title' => $this->faker->title(),
            'is_mandatory'      => 1,
            'is_current'        => 1,
            'force_user_update' => 1,
            'enabled'           => 1,
            'models'            => config('filament-user-consent.user_models'),
            'published_at'      => now(),
        ];
    }

    public static function newFactory()
    {
        $instance = new static();

        // Retrieve the configured model class from the package config
        $instance->model = config('filament-user-consent.models.consent_option');
        return $instance;
    }
}
