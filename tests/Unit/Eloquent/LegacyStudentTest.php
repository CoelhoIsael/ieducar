<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyBenefit;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use App\Models\StudentInep;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Tests\EloquentTestCase;

class LegacyStudentTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'individual' => LegacyIndividual::class,
        'person' => LegacyPerson::class,
        'registrations' => LegacyRegistration::class,
        'inep' => StudentInep::class,
        'benefits' => LegacyBenefit::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return LegacyStudent::class;
    }

    public function testGuardianTypeAttribute(): void
    {
        $this->assertEquals($this->model->tipo_responsavel, $this->model->guardianType);
    }

    public function testGetGuardianName(): void
    {
        $join = $this->model->individual->mother->name . ', ' . $this->model->individual->father->name;
        $expected = match ($this->model->guardianType) {
            'm' => $this->model->individual->mother->name,
            'p' => $this->model->individual->father->name,
            'r' => $this->model->individual->responsible->name,
            'a' => strlen($join) < 3 ? null : $join,
            default => null
        };
        $this->assertEquals($expected, $this->model->getGuardianName());
    }

    public function testGetGuardianCpf(): void
    {
        $join = ($this->model->individual->mother->individual->cpf ?? 'não informado') . ', ' . ($this->individual->model->father->individual->cpf ?? 'não informado');
        $expected = match ($this->model->guardianType) {
            'm' => $this->individual->mother->individual->cpf ?? 'não informado',
            'p' => $this->individual->father->individual->cpf ?? 'não informado',
            'r' => $this->individual->responsible->individual->cpf ?? 'não informado',
            'a' => strlen($join) < 3 ? null : $join,
            default => null
        };
        $this->assertEquals($expected, $this->model->getGuardianCpf());
    }

    public function testInepNumberAttribute(): void
    {
        $this->assertEquals($this->model->inep ? $this->model->inep->number : null, $this->model->inepNumber);
    }

    public function testStateRegistrationIdAttribute(): void
    {
        $this->assertEquals($this->model->aluno_estado_id, $this->model->stateRegistrationId);
    }

    public function testScopeActive(): void
    {
        LegacyStudentFactory::new()->create(['ativo' => 0]);
        $found = $this->instanceNewEloquentModel()->active()->get();
        $this->assertCount(2, $found);
    }

    public function testScopeMale(): void
    {
        $individual = LegacyIndividualFactory::new()->create(['sexo' => 'M']);
        LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual
        ]);
        $found = $this->instanceNewEloquentModel()->male()->get();
        $this->assertCount(1, $found);
    }

    public function testScopeFemale(): void
    {
        $individual = LegacyIndividualFactory::new()->create(['sexo' => 'F']);
        LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual
        ]);
        $found = $this->instanceNewEloquentModel()->female()->get();
        $this->assertCount(1, $found);
    }
}
