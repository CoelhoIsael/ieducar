<?php

namespace App\Models;

use App\Models\Builders\LegacyCourseBuilder;
use App\Traits\HasLegacyDates;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacyCourse
 *
 * @property string        $name
 * @property LegacyGrade[] $grades
 *
 * @method static LegacyCourseBuilder query()
 */
class LegacyCourse extends LegacyModel
{
    use LegacyAttribute;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.curso';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_curso';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacyCourseBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var array
     */
    public array $legacy = [
        'id' => 'cod_curso',
        'name' => 'nm_curso',
        'is_standard_calendar' => 'padrao_ano_escolar',
        'steps' => 'qtd_etapas',
        'description' => 'descricao'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'ref_cod_tipo_regime',
        'ref_cod_nivel_ensino',
        'ref_cod_tipo_ensino',
        'nm_curso',
        'sgl_curso',
        'qtd_etapas',
        'carga_horaria',
        'ref_cod_instituicao',
        'hora_falta',
        'ativo',
        'modalidade_curso',
        'padrao_ano_escolar',
        'multi_seriado'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'padrao_ano_escolar' => 'boolean',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_curso;
    }

    /**
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->descricao;
    }

    /**
     * @return int
     */
    public function getStepsAttribute()
    {
        return $this->qtd_etapas;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        if (empty($this->description)) {
            return $this->nm_curso;
        }

        return $this->nm_curso . ' (' . $this->description . ')';
    }

    /**
     * @return bool
     */
    public function getIsStandardCalendarAttribute()
    {
        return $this->padrao_ano_escolar;
    }

    /**
     * Relacionamento com as series
     *
     * @return HasMany
     */
    public function grades()
    {
        return $this->hasMany(LegacyGrade::class, 'ref_cod_curso');
    }

    /**
     * Relaciona com  as escolas
     *
     * @return BelongsToMany
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(LegacySchool::class, 'escola_curso', 'ref_cod_curso', 'ref_cod_escola')->wherePivot('ativo', 1);
    }

    /**
     * Relaciona com as habilitações
     *
     * @return BelongsToMany
     */
    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(LegacyQualification::class, 'pmieducar.habilitacao_curso', 'ref_cod_curso', 'ref_cod_habilitacao');
    }
}
