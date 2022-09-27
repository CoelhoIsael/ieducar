<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonHasPlace extends Pivot
{
    /**
     * @var array
     */
    protected $fillable = [
        'person_id',
        'place_id',
        'type',
    ];

    protected $relatedKey = 'person_id';
    protected $foreignKey = 'place_id';
    public $incrementing = true;

    /**
     * @return BelongsTo
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
