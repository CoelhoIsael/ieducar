<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyQualification;
use Tests\EloquentTestCase;

class LegacyQualificationTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyQualification::class;
    }
}
