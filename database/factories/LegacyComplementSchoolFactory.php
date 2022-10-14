<?php

namespace Database\Factories;

use App\Models\LegacyComplementSchool;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyComplementSchoolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyComplementSchool::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'id' => fn () => LegacySchoolFactory::new()->create(),
            'name' => $this->faker->name,
            'city' => $this->faker->name,
            'cep' => $this->faker->randomNumber(8),
            'number' => $this->faker->randomNumber(3),
            'complement' => $this->faker->word,
            'address' => $this->faker->address,
            'district' => $this->faker->word,
            'created_by' => fn () => LegacyUserFactory::new()->unique()->make(),
            'ddd_phone' => $this->faker->randomNumber(2),
            'phone' => $this->faker->randomNumber(8),
            'ddd_faz' => $this->faker->randomNumber(2),
            'fax' => $this->faker->randomNumber(8),
        ];
    }
}
