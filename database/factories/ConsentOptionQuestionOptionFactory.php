<?php

namespace Visualbuilder\FilamentUserConsent\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionQuestionOption;

class ConsentOptionQuestionOptionFactory extends Factory
{
    protected $model = ConsentOptionQuestionOption::class;

    public function definition(): array
    {
        return [
            'value'                  => $this->faker->randomNumber(),
            'text'                   => $this->faker->sentence(),
            'sort'                   => $this->faker->randomDigit(),
            'additional_info'        => $info = $this->faker->boolean(),
            'additional_info_label'  => $info ? $this->faker->word() : '',
        ];
    }
}
