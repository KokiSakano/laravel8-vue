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
        return [
            'user_id' => User::factory(),
            'whisper' => $this->faker->sentence,
            'good' => rand(0, 5),
        ];
    }
}
