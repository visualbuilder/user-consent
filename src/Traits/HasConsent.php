<?php

namespace Visualbuilder\FilamentUserConsent\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Visualbuilder\FilamentUserConsent\Models\ConsentOption;
use Visualbuilder\FilamentUserConsent\Models\ConsentOptionUser;

/**
 * Trait for adding to a user model
 */
trait HasConsent
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function requiredConsents()
    {
        $consentOptionModel = config('filament-user-consent.models.consent_option');
        return $consentOptionModel::findbykeys($this->requiredConsentKeys())->get();
    }
    public function requiredConsentKeys(): array
    {
        $consentOptionModel = config('filament-user-consent.models.consent_option');
        return $consentOptionModel::activeKeysForUser($this);
    }

    public function outstandingConsentValidators()
    {
        $consents = $this->outstandingConsents();
        $validationArray = [];
        foreach ($consents as $consent) {
            $validationArray['consent_option.' . $consent->id] = 'boolean|' . ($consent->is_mandatory ? 'accepted' : 'required');
        }

        return $validationArray;
    }

    /**
     * @param  array  $acceptedConsents
     */
    public function requiredOutstandingConsentsValidate($acceptedConsents): bool
    {
        $acceptedConsents = array_map('intval', $acceptedConsents);
        $requiredConsents = [];
        $isValid = true;
        foreach ($this->outstandingConsents() as $key => $consent) {
            if ($consent->is_mandatory) {
                $requiredConsents[] = $consent->id;
                if (! in_array($consent->id, $acceptedConsents)) {
                    $isValid = false;

                    break;
                }
            }
        }

        return $isValid;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function outstandingConsents()
    {
        $consentOptionModel = config('filament-user-consent.models.consent_option');

        $consents =  $consentOptionModel::findbykeys($this->requiredConsentKeys())
            ->whereNotIn(
                'id',
                $this->consents()
                    ->pluck('consent_options.id')
                    ->toArray()
            )
            ->orderBy('sort_order')
            ->where('is_survey', false)
            ->get();

        if(method_exists($user = auth()->user(), 'surveyConsents')) {
            return $consents->merge($user->surveyConsents());
        }
        return $consents;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function consents()
    {
        return $this->morphToMany( config('filament-user-consent.models.consent_option'), 'consentable')
            ->withTimestamps()
            ->withPivot('accepted')
            ->using(ConsentOptionUser::class);
    }

    public function lastConsentByKey($key)
    {
        return $this->consents()->where('consentables.key', $key)->latest()->first();
    }

    public function hasPreviousConsents($key)
    {
        return $this->consents()->where('consentables.key', $key)->count();
    }

    /**
     * @return mixed
     */
    public function activeConsents()
    {

        $usersSeenConsents = DB::table('consentables')
            ->selectRaw('max(consent_option_id) as id')
            ->where('consentable_id', $this->id)
            ->where('consentable_type', get_class($this))
            ->groupBy('key')
            ->pluck('id')
            ->toArray();

        return $this->consents()
            ->wherePivotIn('consent_option_id', $usersSeenConsents)
            ->withPivot(['accepted', 'id','fields']);
    }

    /**
     * @return bool
     */
    public function hasRequiredConsents()
    {
        // Define a unique cache key based on identifiable attributes including the model class
        $cacheKey = 'user_consent_'.class_basename($this).'_'.$this->getKey();

        // Retrieve from cache or calculate if not cached
        return Cache::rememberForever($cacheKey, function () {
            $consentOptionModel =  config('filament-user-consent.models.consent_option');
            $requiredConsents = $consentOptionModel::findbykeys($this->requiredConsentKeys())
                ->where('force_user_update', true)
                ->pluck('id')
                ->toArray();
            $givenConsents = $this->consents()
                ->pluck('consent_options.id')
                ->toArray();

            return !array_diff($requiredConsents, $givenConsents);
        });
    }
}
