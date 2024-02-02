<?php

namespace Visualbuilder\FilamentUserConsent\Traits;

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
        return ConsentOption::findbykeys($this->requiredConsentKeys())->get();
    }

    public function requiredConsentKeys(): array
    {
        return ConsentOption::getAllActiveKeysbyUserClass(class_basename($this));
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
     * @param array $acceptedConsents
     * @return bool
     */
    public function requiredOutstandingConsentsValidate($acceptedConsents): bool
    {
        $acceptedConsents = array_map('intval', $acceptedConsents);
        $requiredConsents = [];
        $isValid = true;
        foreach ($this->outstandingConsents() as $key => $consent) {
            if($consent->is_mandatory) {
                $requiredConsents[] = $consent->id;
                if(!in_array($consent->id, $acceptedConsents)) {
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
        return ConsentOption::findbykeys($this->requiredConsentKeys())
            ->whereNotIn(
                'id',
                $this->consents()
                    ->pluck('consent_options.id')
                    ->toArray()
            )
            ->orderBy('sort_order')
            ->get();

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function consents()
    {
        return $this->morphToMany(ConsentOption::class, 'consentable')
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
            ->withPivot(['accepted', 'id']);
    }

    /**
     * @return bool
     */
    /*
    public function hasRequiredConsents()
    {
        // Query for required consent IDs directly, instead of getting keys first
        $requiredConsentIdsQuery = ConsentOption::query()
            ->whereIn(
                'key',
                ConsentOption::where('models', 'like', '%' . class_basename($this) . '%')
                    ->where('is_current', true)
                    ->where('enabled', true)
                    ->where('published_at', '<=', now())
                    ->pluck('key')
            )
            ->where('force_user_update', true)
            ->where('is_current', true)
            ->where('enabled', true)
            ->whereDate('published_at', '<=', now())
            ->select('id');  // select 'id' for the subquery

        // Use exists() in a subquery for performance
        return ! $this->consents()
            ->whereNotExists(function ($query) use ($requiredConsentIdsQuery) {
                $query->select(DB::raw(1))
                    ->from(DB::raw("({$requiredConsentIdsQuery->toSql()}) as sub"))
                    ->whereIn('sub.id', $this->consents()->pluck('consent_options.id'))
                    ->mergeBindings($requiredConsentIdsQuery->getQuery());
            })->exists();
    }
    */

    /**
     * @return bool
     */
    public function hasRequiredConsents()
    {
        $requiredConsents = ConsentOption::findbykeys($this->requiredConsentKeys())
            ->where('force_user_update', true)
            ->pluck('id')
            ->toArray();
        $givenConsents = $this->consents()
            ->pluck('consent_options.id')
            ->toArray();

        return ! array_diff($requiredConsents, $givenConsents);
    }
}
