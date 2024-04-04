<?php

namespace Visualbuilder\FilamentUserConsent\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * @property int $id
 * @property int $consent_option_id
 * @property int $consentable_id
 * @property string $consentable_type
 * @property string $key
 * @property bool $accepted
 * @property string $created_at
 * @property string $updated_at
 */
class ConsentOptionUser extends MorphPivot
{
    public $incrementing = true;

    protected $table = 'consentables';

    public static function getAllSavedUserTypes(): array
    {
        return self::query()->select('consentable_type')->distinct()->pluck('consentable_type')->toArray();
    }

    public function consentable()
    {
        return $this->morphTo();
    }

    public function consentOption()
    {
        return $this->belongsTo(ConsentOption::class, 'consent_option_id', 'id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ConsentableResponse::class, 'consentable_id', 'id');
    }

    /**
     * @return $this
     */
    public function toggleStatus()
    {
        $this->accepted = ! $this->accepted;

        return $this;
    }
}
