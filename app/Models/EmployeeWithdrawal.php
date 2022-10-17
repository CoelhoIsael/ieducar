<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWithdrawal extends LegacyModel
{
    use HasFiles;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    protected $table = 'pmieducar.servidor_afastamento';

    protected $fillable = [
        'ref_cod_servidor',
        'sequencial',
        'ref_ref_cod_instituicao',
        'ref_cod_motivo_afastamento',
        'data_retorno',
        'data_saida',
    ];

    protected $dates = [
        'data_retorno',
        'data_saida',
    ];

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'ref_cod_servidor', 'cod_servidor');
    }

    /**
     * @return BelongsTo
     */
    public function reason()
    {
        return $this->belongsTo(WithdrawalReason::class, 'ref_cod_motivo_afastamento', 'cod_motivo_afastamento');
    }
}
