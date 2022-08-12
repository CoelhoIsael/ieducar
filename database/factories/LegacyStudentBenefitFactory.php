<?php

namespace Database\Factories;

use App\Models\LegacyStudentBenefit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyStudentBenefit>
 */
class LegacyStudentBenefitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyStudentBenefit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'aluno_id' => LegacyStudentFactory::new()->make(),
            'aluno_beneficio_id' => LegacyBenefitFactory::new()->make(),
        ];
    }
}
