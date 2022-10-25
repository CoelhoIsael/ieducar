<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Models\LegacyUser;
use App\Models\MigratedDiscipline;
use Tests\EloquentTestCase;

class MigratedDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'oldDiscipline' => LegacyDiscipline::class,
        'newDiscipline' => LegacyDiscipline::class,
        'grade' => LegacyGrade::class,
        'createdBy' => LegacyUser::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return MigratedDiscipline::class;
    }
}
