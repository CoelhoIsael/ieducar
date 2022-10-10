<?php

namespace Tests\Unit\Eloquent;

use App\Models\Person;
use App\Models\PersonHasPlace;
use App\Models\Place;
use Tests\EloquentTestCase;

class PersonHasPlaceTest extends EloquentTestCase
{
    protected $relations = [
        'place' => Place::class,
        'person' => Person::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return PersonHasPlace::class;
    }
}
