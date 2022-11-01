<?php

namespace App\Models;

use App\Traits\HasLegacyDates;
use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyRegistration
 *
 * @property integer              $id
 * @property boolean              $isTransferred
 * @property boolean              $isAbandoned
 * @property boolean              $isCanceled
 * @property boolean              $bloquear_troca_de_situacao
 * @property boolean              $dependencia
 * @property integer              $cod_matricula
 * @property integer              $ano
 * @property LegacyStudentAbsence $studentAbsence
 * @property LegacyStudentScore   $studentScore
 * @property LegacyCourse         $course
 * @property Collection           $enrollments
 */
class LegacyRegistration extends LegacyModel
{
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula';

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = 'updated_at';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ref_cod_curso',
        'ref_cod_aluno',
        'ano',
        'ref_usuario_cad',
        'dependencia',
        'ativo',
        'aprovado',
        'data_matricula',
        'ultima_matricula',
        'bloquear_troca_de_situacao'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'data_matricula'
    ];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $this->cod_matricula
        );
    }

    public function isLockedToChangeStatus(): bool
    {
        return (bool)$this->bloquear_troca_de_situacao;
    }

    protected function isDependency(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $this->dependencia
        );
    }

    protected function year(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $this->ano
        );
    }

    /**
     * Relação com o aluno.
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * Relação com a escola.
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * Relação com a série.
     *
     * @return BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_ref_cod_serie');
    }

    /**
     * Relação com o curso.
     *
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * @return HasMany
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula');
    }

    /**
     * @return HasMany
     */
    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula')->where('ativo', 1);
    }

    /**
     * @return HasOne
     */
    public function lastEnrollment()
    {
        return $this->hasOne(LegacyEnrollment::class, 'ref_cod_matricula')->orderBy('sequencial', 'DESC');
    }

    /**
     * @return HasMany
     */
    public function exemptions(): HasMany
    {
        return $this->hasMany(LegacyDisciplineExemption::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('matricula.ativo', 1);
    }

    protected function isTransferred(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $this->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO
        );
    }

    protected function isAbandoned(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $this->aprovado == App_Model_MatriculaSituacao::ABANDONO
        );
    }

    protected function isCanceledA(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $this->ativo === 0
        );
    }

    /**
     * @return HasOne
     */
    public function studentAbsence(): HasOne
    {
        return $this->hasOne(LegacyStudentAbsence::class, 'matricula_id');
    }

    /**
     * @return HasOne
     */
    public function studentScore(): HasOne
    {
        return $this->hasOne(LegacyStudentScore::class, 'matricula_id');
    }

    /**
     * @return HasOne
     */
    public function studentDescriptiveOpinion(): HasOne
    {
        return $this->hasOne(LegacyStudentDescriptiveOpinion::class, 'matricula_id');
    }

    /**
     * @return HasMany
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(LegacyDisciplineDependence::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @return LegacyEvaluationRule
     */
    public function getEvaluationRule()
    {
        $evaluationRuleGradeYear = $this->hasOne(LegacyEvaluationRuleGradeYear::class, 'serie_id', 'ref_ref_cod_serie')
            ->where('ano_letivo', $this->ano)
            ->firstOrFail();

        if ($this->school->utiliza_regra_diferenciada && $evaluationRuleGradeYear->differentiatedEvaluationRule) {
            return $evaluationRuleGradeYear->differentiatedEvaluationRule;
        }

        return $evaluationRuleGradeYear->evaluationRule;
    }

    protected function statusDescription(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (new RegistrationStatus())->getDescriptiveValues()[(int)$this->aprovado]
        );
    }

    public function scopeMale(Builder $query): Builder
    {
        return $query->join('pmieducar.aluno', 'aluno.cod_aluno', '=', 'matricula.ref_cod_aluno')
            ->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('aluno.ativo', 1)
            ->where('sexo', 'M');
    }

    public function scopeFemale(Builder $query): Builder
    {
        return $query->join('pmieducar.aluno', 'aluno.cod_aluno', '=', 'matricula.ref_cod_aluno')
            ->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('aluno.ativo', 1)
            ->where('sexo', 'F');
    }

    public function scopeLastYear(Builder $query): Builder
    {
        return $query->where('matricula.ano', date('Y') - 1);
    }

    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->where('matricula.ano', date('Y'));
    }
}
