<?php

namespace Visualbuilder\FilamentUserConsent\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionQuestion;

class ConsentOptionQuestionFactory extends Factory
{
    protected $model = ConsentOptionQuestion::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true);

        return [
            'component'             =>  Arr::random(config('filament-user-consent.components')),
            'name'                  => $this->randomString(),
            'label'                 => $title,
            'required'              => $this->faker->boolean(),
            'sort'                  => $this->faker->randomNumber(),
            'content'               => $this->faker->sentence(),
            'default_user_column'   => null,
        ];
    }

    /**
   * Generate Random String
   * @param Int Length of string(50)
   * @param Bool Upper Case(True,False)
   * @param Bool Numbers(True,False)
   * @param Bool Special Chars(True,False)
   * @return String  Random String
   */
    public function randomString($length = 10, $uc = false, $n = false, $sc = false) 
    {
        $rstr='';
        $source = 'abcdefghijklmnopqrstuvwxyz';
        if ($uc)
            $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($n)
            $source .= '1234567890';
        if ($sc)
            $source .= '|@#~$%()=^*+[]{}-_';
        if ($length > 0) {
            $rstr = "";
            $length1= $length-1;
            $input = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
            $rand = array_rand($input, 1);
            $source = str_split($source, 1);
            $rstr1 = '';
            for ($i = 1; $i <= $length1; $i++) {
                $num = mt_rand(1, count($source));
                $rstr1 .= $source[$num - 1];
                $rstr = "{$rand}{$rstr1}";
            }
        }
        return $rstr;
    }
}
