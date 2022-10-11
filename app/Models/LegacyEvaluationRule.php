<?php

namespace App\Models;

use App\Models\Builders\LegacyEvaluationRuleBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyEvaluationRule
 *
 * @method static LegacyEvaluationRuleBuilder query()
 */
class LegacyEvaluationRule extends Model
{
    use LegacyAttribute;

    public const PARALLEL_REMEDIAL_NONE = 0;
    public const PARALLEL_REMEDIAL_PER_STAGE = 1;
    public const PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE = 2;

    public const PARALLEL_REMEDIAL_REPLACE_SCORE = 1;
    public const PARALLEL_REMEDIAL_AVERAGE_SCORE = 2;
    public const PARALLEL_REMEDIAL_SUM_SCORE = 3;

    /**
     * @var string
     */
    protected $table = 'modules.regra_avaliacao';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $casts = [
        'media_recuperacao_paralela' => 'float',
    ];

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacyEvaluationRuleBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'name' => 'nome'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'instituicao_id', 'nome', 'formula_media_id', 'formula_recuperacao_id', 'tipo_nota', 'tipo_progressao', 'tipo_presenca',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function remedialRules()
    {
        return $this->hasMany(LegacyRemedialRule::class, 'regra_avaliacao_id');
    }

    /**
     * @return HasOne
     */
    public function roundingTable()
    {
        return $this->hasOne(LegacyRoundingTable::class, 'id', 'tabela_arredondamento_id');
    }

    /**
     * @return HasOne
     */
    public function conceptualRoundingTable()
    {
        return $this->hasOne(LegacyRoundingTable::class, 'id', 'tabela_arredondamento_id_conceitual');
    }

    /**
     * @return HasOne
     */
    public function deficiencyEvaluationRule()
    {
        return $this->hasOne(LegacyEvaluationRule::class, 'id', 'regra_diferenciada_id');
    }

    /**
     * @return bool
     */
    public function isAverageBetweenScoreAndRemedialCalculation()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE
            && $this->tipo_calculo_recuperacao_paralela == self::PARALLEL_REMEDIAL_AVERAGE_SCORE;
    }

    /**
     * @return bool
     */
    public function isSpecificRetake()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE;
    }

    /**
     * @return bool
     */
    public function isSumScoreCalculation()
    {
        return $this->tipo_recuperacao_paralela == self::PARALLEL_REMEDIAL_PER_STAGE
            && $this->tipo_calculo_recuperacao_paralela == self::PARALLEL_REMEDIAL_SUM_SCORE;
    }

    /**
     * @return bool
     */
    public function isGlobalScore()
    {
        return $this->nota_geral_por_etapa == 1;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nome;
    }
}
