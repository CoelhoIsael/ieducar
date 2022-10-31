<?php

namespace App\Models;

use App\Support\Database\DateSerializer;
use App\Traits\HasLegacyDates;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacyEnrollment
 *
 * @property int                $id
 * @property int                $registration_id
 * @property int                $school_class_id
 * @property int                $etapa_educacenso
 * @property string             $studentName
 * @property DateTime           $date
 * @property LegacyRegistration $registration
 * @property LegacySchoolClass  $schoolClass
 */
class LegacyEnrollment extends LegacyModel
{
    use DateSerializer;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula_turma';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_turma',
        'sequencial',
        'ref_usuario_cad',
        'data_enturmacao',
        'sequencial_fechamento',
        'remanejado_mesma_turma',
        'ativo',
        'tipo_itinerario',
        'composicao_itinerario',
        'curso_itinerario',
        'itinerario_concomitante',
        'etapa_educacenso'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'data_enturmacao', 'data_exclusao'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->data_enturmacao,
        );
    }

    protected function dateDeparted(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->data_exclusao,
        );
    }

    protected function schoolClassId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->ref_cod_turma,
        );
    }

    protected function registrationId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->ref_cod_matricula,
        );
    }

    protected function studentName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->registration->student->person->nome,
        );
    }

    /**
     * Relação com a matrícula.
     *
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }

    /**
     * Relação com a turma.
     *
     * @return BelongsTo
     */
    public function schoolClass()
    {
        return $this->belongsTo(LegacySchoolClass::class, 'ref_cod_turma');
    }

    /**
     * Retorna o turno do aluno.
     *
     * Relação com turma_turno.
     *
     * @return bool | string
     */
    public function period()
    {
        return $this->belongsTo(LegacyPeriod::class, 'turno_id')->withDefault();
    }

    /**
     * Relação com servidor.
     *
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(LegacyUser::class, 'ref_usuario_cad');
    }

    /**
     * Relação com servidor.
     *
     * @return BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(LegacyUser::class, 'ref_usuario_exc');
    }

    public function getStudentId()
    {
        return $this->registration->student->cod_aluno;
    }
}
