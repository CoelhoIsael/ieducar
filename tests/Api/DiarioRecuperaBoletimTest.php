<?php

namespace Tests\Api;

use Database\Factories\LegacyEnrollmentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaBoletimTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaBoletimEstrutura()
    {
        $enrollment = LegacyEnrollmentFactory::new()->create();
        $data = [
            'oper'=> 'get',
            'resource' => 'boletim',
            'matricula_id' =>  $enrollment->ref_cod_matricula,
            'escola_id' =>  $enrollment->schoolClass()->first()->schoolId
        ];

        $response = $this->getResource('/module/Api/Report?', $data);

        $response->assertJsonStructure(
            [
                'oper',
                'resource',
                'msgs',
                'any_error_msg',
                'encoded',
                'matricula_id',
            ]
        );
    }
}
