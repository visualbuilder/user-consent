<?php

namespace Visualbuilder\FilamentUserConsent\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $consent_option_id
 * @property int $consentable_id
 * @property string $consentable_type
 * @property string $key
 * @property bool $accepted
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ConsentOptionUser extends MorphPivot
{
    public $incrementing = true;

    public $casts = [
        'consent_option_id' => 'integer',
        'consentable_id'    => 'integer',
        'accepted'          => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    protected $table = 'consentables';

    public static function getAllSavedUserTypes(): array
    {
        return self::query()->select('consentable_type')->distinct()->pluck('consentable_type')->toArray();
    }

    public function consentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function consentOption(): BelongsTo
    {
        return $this->belongsTo(ConsentOption::class, 'consent_option_id', 'id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ConsentableResponse::class, 'consentable_id', 'id');
    }

    public function getResponseByField($fieldName) {
        return $this->responses->where('question_field_name', $fieldName)->first()->response ?? '';
    }

    /**
     * @return static
     */
    public function toggleStatus(): static
    {
        $this->accepted = !$this->accepted;
        return $this;
    }
}
