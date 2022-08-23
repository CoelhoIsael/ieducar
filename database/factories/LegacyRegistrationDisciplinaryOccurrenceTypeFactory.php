<?php

namespace Database\Factories;

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyRegistrationDisciplinaryOccurrenceType>
 */
class LegacyRegistrationDisciplinaryOccurrenceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(),
            'ref_cod_tipo_ocorrencia_disciplinar' => LegacyDisciplinaryOccurrenceTypeFactory::new()->create(),
            'sequencial' => 1,
            'ref_usuario_exc' => null,
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'observacao' => $this->faker->paragraph(),
            'data_exclusao' => null,
            'visivel_pais' => $this->faker->boolean()
        ];
    }
}
