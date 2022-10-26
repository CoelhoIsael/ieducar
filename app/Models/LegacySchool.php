<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacySchool
 *
 * @property string            $name
 * @property LegacyInstitution $institution
 *
 * @method static LegacySchoolBuilder query()
 */
class LegacySchool extends Model
{
    use LegacyAttribute;

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected string $builder = LegacySchoolBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public $legacy = [
        'id' => 'cod_escola',
        'name' => 'fantasia'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'cod_escola',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'ref_cod_instituicao',
        'sigla',
        'data_cadastro',
        'data_exclusao',
        'ref_idpes',
        'ativo',
        'orgao_vinculado_escola',
        'situacao_funcionamento',
        'zona_localizacao',
        'localizacao_diferenciada',
        'dependencia_administrativa',
        'mantenedora_escola_privada',
        'categoria_escola_privada',
        'conveniada_com_poder_publico',
        'cnpj_mantenedora_principal',
        'regulamentacao',
        'esfera_administrativa',
        'unidade_vinculada_outra_instituicao',
        'inep_escola_sede',
        'codigo_ies',
        'qtd_vice_diretor',
        'qtd_orientador_comunitario',
        'latitude',
        'longitude',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->cod_escola,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->organization->fantasia ?? null,
        );
    }

    /**
     * Relacionamento com a instituição.
     *
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }

    /**
     * Anos letivos
     *
     * @return HasMany
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(LegacySchoolAcademicYear::class, 'ref_cod_escola');
    }

    /**
     * @return BelongsTo
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    /**
     * @return BelongsToMany
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyCourse::class,
            'pmieducar.escola_curso',
            'ref_cod_escola',
            'ref_cod_curso'
        )->withPivot('ativo', 'anos_letivos');
    }

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(LegacyOrganization::class, 'ref_idpes');
    }

    /**
     * @return HasOne
     */
    public function inep(): HasOne
    {
        return $this->hasOne(SchoolInep::class, 'cod_escola', 'cod_escola');
    }

    /**
     * @return BelongsToMany
     */
    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyGrade::class,
            'pmieducar.escola_serie',
            'ref_cod_escola',
            'ref_cod_serie'
        )->withPivot('ativo', 'anos_letivos', 'bloquear_enturmacao_sem_vagas');
    }

    /**
     * @return HasMany
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_ref_cod_escola');
    }

    /**
     * @return HasMany
     */
    public function schoolUsers(): HasMany
    {
        return $this->hasMany(LegacyUserSchool::class, 'ref_cod_escola', 'cod_escola');
    }

    /**
     * @return HasMany
     */
    public function schoolManagers(): HasMany
    {
        return $this->hasMany(SchoolManager::class, 'school_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(LegacyAcademicYearStage::class, 'ref_ref_cod_escola');
    }

    /**
     * @return Enrollment[]
     */
    public function enrollments(): array
    {
        return $this->hasMany(Enrollment::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * Retorna os dias da semana em um array
     *
     * @param string $value
     *
     * @return array|null
     */
    public function getDiasSemanaAttribute($value)
    {
        if (is_string($value)) {
            $value = explode(',', str_replace(['{', '}'], '', $value));
        }

        return (array) $value;
    }

    /**
     * Seta os dias da semana transformando um array em uma string
     *
     * @param array $values
     *
     * @return void
     */
    public function setDiasSemanaAttribute($values)
    {
        if (is_array($values)) {
            $values = '{' . implode(',', $values) . '}';
        }

        $this->attributes['dias_semana'] = $values;
    }
}
