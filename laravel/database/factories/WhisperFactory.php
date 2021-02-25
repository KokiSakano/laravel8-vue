<?php

namespace Database\Factories;

use App\Models\Whisper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WhisperFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Whisper::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        return [
            'user_id' => $user->id,
            'whisp' => $this->faker->sentence,
            'good' => rand(0, 5),
        ];
    }
}
