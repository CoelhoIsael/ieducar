<?php

namespace App\Models\Exporter\Builders;

use App\Support\Database\JoinableBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class EnrollmentEloquentBuilder extends Builder
{
    use JoinableBuilder;

    /**
     * Colunas legadas usadas para gerar a query do exportador dinámicamente sem a view
     *
     */
    public function getLegacyColumns(): array
    {
        return [
            'mother.person' => [
                'id' => 'm.idpes as ID da mãe',
                'name' => 'm.nome as Nome da mãe',
                'email' => 'm.email as E-mail da mãe'
            ],
            'mother.individual' => [
                'social_name' => 'mf.nome_social as Nome social e/ou afetivo da mãe',
                'cpf' => 'mf.cpf as CPF da mãe',
                'date_of_birth' => 'mf.data_nasc as Data de nascimento da mãe',
                'sus' => 'mf.sus as Número SUS da mãe',
                'nis' => 'mf.nis_pis_pasep as NIS (PIS/PASEP) da mãe',
                'occupation' => 'mf.ocupacao as Ocupação da mãe',
                'organization' => 'mf.empresa as Empresa da mãe',
                'monthly_income' => 'mf.renda_mensal as Renda Mensal da mãe',
                'gender' => 'mf.sexo as Gênero da mãe'
            ],
            'mother.document' => [
                'rg' => 'md.rg as RG da mãe',
                'rg_issue_date' => 'md.data_exp_rg as RG (Data Emissão) da mãe',
                'rg_state_abbreviation' => 'md.sigla_uf_exp_rg as RG (Estado) da mãe'
            ],
            'father.person' => [
                'id' => 'f.idpes as ID do pai',
                'name' => 'f.nome as Nome do pai',
                'email' => 'f.email as E-mail do pai'
            ],
            'father.individual' => [
                'social_name' => 'ff.nome_social as Nome social e/ou afetivo do pai',
                'cpf' => 'ff.cpf as CPF do pai',
                'date_of_birth' => 'ff.data_nasc as Data de nascimento do pai',
                'sus' => 'ff.sus as Número SUS do pai',
                'nis' => 'ff.nis_pis_pasep as NIS (PIS/PASEP) do pai',
                'occupation' => 'ff.ocupacao as Ocupação do pai',
                'organization' => 'ff.empresa as Empresa do pai',
                'monthly_income' => 'ff.renda_mensal as Renda Mensal do pai',
                'gender' => 'ff.sexo as Gênero do pai'
            ],
            'father.document' => [
                'rg' => 'fd.rg as RG do pai',
                'rg_issue_date' => 'fd.data_exp_rg as RG (Data Emissão) do pai',
                'rg_state_abbreviation' => 'fd.sigla_uf_exp_rg as RG (Estado) do pai'
            ],
            'guardian.person' => [
                'id' => 'g.idpes as ID do responsável',
                'name' => 'g.nome as Nome do responsável',
                'email' => 'g.email as E-mail do responsável'
            ],
            'guardian.individual' => [
                'social_name' => 'gf.nome_social as Nome social e/ou afetivo do responsável',
                'cpf' => 'gf.cpf as CPF do responsável',
                'date_of_birth' => 'gf.data_nasc as Data de nascimento do responsável',
                'sus' => 'gf.sus as Número SUS do responsável',
                'nis' => 'gf.nis_pis_pasep as NIS (PIS/PASEP) do responsável',
                'occupation' => 'gf.ocupacao as Ocupação do responsável',
                'organization' => 'gf.empresa as Empresa do responsável',
                'monthly_income' => 'gf.renda_mensal as Renda Mensal do responsável',
                'gender' => 'gf.sexo as Gênero do responsável'
            ],
            'guardian.document' => [
                'rg' => 'gd.rg as RG do pai',
                'rg_issue_date' => 'gd.data_exp_rg as RG (Data Emissão) do responsável',
                'rg_state_abbreviation' => 'gd.sigla_uf_exp_rg as RG (Estado) do responsável'
            ]
        ];
    }

    /**
     * @param array $columns
     *
     * @return void
     */
    public function mother($columns)
    {
        //pessoa
        if ($only = $this->model->getLegacyExportedColumns('mother.person', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.pessoa as m', 'exporter_student.mother_id', 'm.idpes');
        }

        //fisica
        if ($only = $this->model->getLegacyExportedColumns('mother.individual', $columns)) {
            $this->addSelect($only);
            $this->join('cadastro.fisica as mf', 'mf.idpes', 'm.idpes');
        }

        //documento
        if ($only = $this->model->getLegacyExportedColumns('mother.document', $columns)) {
            $this->addSelect($only);
            $this->join('cadastro.documento as md', 'md.idpes', 'm.idpes');
        }
    }

    /**
     * @param array $columns
     *
     * @return void
     */
    public function father($columns)
    {
        //pessoa
        if ($only = $this->model->getLegacyExportedColumns('father.person', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.pessoa as f', 'exporter_student.father_id', 'f.idpes');
        }

        //fisica
        if ($only = $this->model->getLegacyExportedColumns('father.individual', $columns)) {
            $this->addSelect($only);
            $this->join('cadastro.fisica as ff', 'ff.idpes', 'f.idpes');
        }

        //documento
        if ($only = $this->model->getLegacyExportedColumns('mother.document', $columns)) {
            $this->addSelect($only);
            $this->join('cadastro.documento as fd', 'fd.idpes', 'f.idpes');
        }
    }

    /**
     * @param array $columns
     *
     * @return EnrollmentEloquentBuilder
     */
    public function guardian($columns)
    {
        //pessoa
        if ($only = $this->model->getLegacyExportedColumns('guardian.person', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.pessoa as g', 'exporter_student.guardian_id', 'g.idpes');
        }

        //fisica
        if ($only = $this->model->getLegacyExportedColumns('guardian.individual', $columns)) {
            $this->addSelect($only);
            $this->join('cadastro.fisica as gf', 'gf.idpes', 'g.idpes');
        }

        //documento
        if ($only = $this->model->getLegacyExportedColumns('guardian.document', $columns)) {
            $this->addSelect($only);
            $this->join('cadastro.documento as gd', 'gd.idpes', 'g.idpes');
        }
    }

    /**
     * @return EnrollmentEloquentBuilder
     */
    public function benefits()
    {
        $this->addSelect(
            $this->joinColumns('benefits', ['benefits'])
        );

        return $this->leftJoin('exporter_benefits as benefits', function (JoinClause $join) {
            $join->on('exporter_student.student_id', '=', 'benefits.student_id');
        });
    }

    /**
     * @return EnrollmentEloquentBuilder
     */
    public function disabilities()
    {
        $this->addSelect(
            $this->joinColumns('disabilities', ['disabilities'])
        );

        return $this->leftJoin('exporter_disabilities as disabilities', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'disabilities.person_id');
        });
    }

    /**
     * @return EnrollmentEloquentBuilder
     */
    public function phones()
    {
        $this->addSelect(
            $this->joinColumns('phones', ['phones'])
        );

        return $this->leftJoin('exporter_phones as phones', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'phones.person_id');
        });
    }

    /**
     * @param array $columns
     *
     * @return EnrollmentEloquentBuilder
     */
    public function place($columns)
    {
        $this->addSelect(
            $this->joinColumns('place', $columns)
        );

        return $this->leftJoin('person_has_place', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'person_has_place.person_id');
        })->leftJoin('addresses as place', function (JoinClause $join) {
            $join->on('person_has_place.place_id', '=', 'place.id');
        });
    }
}
