<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;

class WithdrawalReason extends LegacyModel
{
    use HasLegacyDates;
    use HasLegacyUserAction;
    use HasInstitution;
    use LegacySoftDeletes;

    protected $table = 'pmieducar.motivo_afastamento';

    protected $primaryKey = 'cod_motivo_afastamento';

    protected $fillable = ['nm_motivo', 'descricao'];

    public array $legacy = [
        'id' => 'cod_motivo_afastamento',
        'name' => 'nm_motivo',
        'description' => 'descricao'
    ];
}
