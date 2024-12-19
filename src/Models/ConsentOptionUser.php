<?php

namespace Visualbuilder\FilamentUserConsent\Models;

use App\Models\Scopes\AssociateConsentScope;
use App\Models\Scopes\EndUserConsentScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;

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


    protected static function booted()
    {

        static::saved(function ($consentOptionUser) {
            self::clearCache($consentOptionUser);
        });

        static::deleted(function ($consentOptionUser) {
            self::clearCache($consentOptionUser);
        });
    }

    public static function clearCache($consentOptionUser)
    {
        $userKey = class_basename($consentOptionUser->consentable_type).'_'.$consentOptionUser->consentable_id;
        $cacheKey1 = 'consent_option_active_keys_for_'.$userKey;
        $cacheKey2 = 'user_consent_'.$userKey;

        // Similarly, flush the cache when a consent option user relationship is deleted
        // Flush the cache for the specific user involved using a simplified class name
        Cache::tags([
            'user-consents',
            $cacheKey1
        ])->flush();

        Cache::tags([
            'user-consents',
            $cacheKey2
        ])->flush();
    }

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
        return $this->belongsTo(config('filament-user-consent.models.consent_option'), 'consent_option_id', 'id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ConsentableResponse::class, 'consentable_id', 'id');
    }

    public function getResponseByField($fieldName) {
        return $this->responses->where('question_field_name', $fieldName)->first()->response ?? '';
    }

    public function getAdditionalInfoByField($fieldName) {
        return $this->responses->where('question_field_name', $fieldName)->first()->additional_info ?? '';
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
