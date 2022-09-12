<?php

namespace Database\Factories;

use App\Models\LegacySchool;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolFactory extends Factory
{
    protected $model = LegacySchool::class;

    public function definition(): array
    {
        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make(),
            'ref_cod_escola_rede_ensino' => LegacyEducationNetworkFactory::new()->create(),
            'sigla' => $this->faker->asciify(),
            'data_cadastro' => now(),
            'ref_idpes' => LegacyOrganizationFactory::new()->create(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }

    public function withPhone(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            LegacyPhoneFactory::new()->create([
                'idpes' => $school->person,
                'tipo' => 1,
            ]);
        });
    }
}
