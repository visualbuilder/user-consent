<?php

namespace Visualbuilder\FilamentUserConsent\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Visualbuilder\FilamentUserConsent\Database\Factories\ConsentOptionFactory;
use Visualbuilder\FilamentUserConsent\Traits\UserCount;

/**
 * @property int $id
 * @property string $key
 * @property int $version
 * @property string $title
 * @property string $label
 * @property string $text
 * @property bool $is_mandatory
 * @property bool $is_current
 * @property bool $enabled
 * @property bool $force_user_update
 * @property int $sort_order
 * @property array $models
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $published_at
 */
class ConsentOption extends Model
{
    use HasFactory;
    // use UserCount;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var string[]
     */
    protected $fillable = [
        'key',
        'version',
        'title',
        'label',
        'text',
        'is_mandatory',
        'force_user_update',
        'is_current',
        'enabled',
        'sort_order',
        'models',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'models' => 'array',
        'published_at' => 'datetime:Y-m-d H:i:s',
        // 'enabled' => 'boolean',
        // 'is_current' => 'boolean',
        // 'force_user_update' => 'boolean',
        // 'is_mandatory' => 'boolean',
    ];

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title . ' V' . $this->version;
    }

    /**
     * @return mixed
     */
    public static function findbykeys($keys)
    {
        return self::query()
            ->whereIn('key', $keys)
            ->where('is_current', true)
            ->where('enabled', true)
            ->whereDate(
                'published_at',
                '<=',
                now()
            );
    }

    public static function allActiveConsents()
    {
        return self::query()
            ->where('is_current', true)
            ->where('enabled', true)
            ->whereDate(
                'published_at',
                '<=',
                now()
            );
    }

    /**
     * @return mixed
     */
    public static function getAllUserTypes()
    {
        $defaults = config('filament-user-consent.models');
        $models = collect([]);
        foreach ($defaults as $model) {
            $models->push([
                'id' => $model,
                'name' => self::modelBasename($model),
                'relation' => strtolower(Str::plural(self::modelBasename($model))),
            ]);
        }

        return $models;
    }

    /**
     * @return string
     */
    public static function modelBasename($model)
    {
        return substr($model, strrpos($model, '\\') + 1);
    }

    public static function getAllActiveKeysbyUserClass($className): array
    {
        return self::where('models', 'like', "%$className%")
            ->where('is_current', true)
            ->where('enabled', true)
            ->where('published_at', '<=', \Illuminate\Support\Carbon::now())
            ->pluck('key')
            ->toArray();
    }

    public static function getAllKeys(): array
    {
        return self::query()
            ->select('key')
            ->distinct()
            ->pluck('key')
            ->toArray();
    }

    public static function getAllKeysCount(): int
    {
        return DB::table('consent_options')
            ->distinct('key')
            ->count('key');
    }

    /**
     * @return ConsentOptionFactory
     */
    protected static function newFactory()
    {
        return ConsentOptionFactory::new();
    }

    public function nextConsentReadyToActivate()
    {
        return ConsentOption::query()
            ->where('is_current', false)
            ->where('enabled', false)
            ->whereDate('published_at', '<=', now())
            ->whereDate('published_at', '>=', $this->published_at)
            ->first();
    }

    /**
     * @return mixed
     */
    public function lastVersionUserSeen($user)
    {
        $lastSeenVersionId = DB::table('consentables')
            ->where('key', $this->key)
            ->where('consentable_id', $user->id)
            ->where('consentable_type', get_class($user))
            ->max('consent_option_id');

        return ConsentOption::findOrFail($lastSeenVersionId);
    }

    /**
     * @return int
     */
    public function usersAcceptedCount()
    {
        return DB::table('consentables')
            ->where('consent_option_id', $this->id)
            ->where('accepted', true)
            ->count();
    }

    /**
     * @return int
     */
    public function getUsersViewedTotalAttribute()
    {
        return DB::table('consentables')
            ->where('key', $this->key)
            ->count();
    }

    /**
     * @return int
     */
    public function getUsersViewedThisVersionAttribute()
    {
        return DB::table('consentables')
            ->where('consent_option_id', $this->id)
            ->count();
    }

    /**
     * @return int
     */
    public function getUsersAcceptedTotalAttribute()
    {
        return DB::table('consentables')
            ->where('accepted', true)
            ->where('key', $this->key)
            ->count();
    }

    /**
     * @return int
     */
    public function getUsersDeclinedTotalAttribute()
    {
        return DB::table('consentables')
            ->where('accepted', false)
            ->where('key', $this->key)
            ->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllVersionsForSelect()
    {
        return self::newQuery()
            ->select('id')
            ->selectRaw('concat("Ver. ",version) as name')
            ->where('key', $this->key)
            ->get();
    }

    /**
     * @return $this
     */
    public function setCurrentVersion()
    {
        $this->disableAllVersions()
            ->fresh()
            ->update(['is_current' => true, 'enabled' => true]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function disableAllVersions()
    {
        self::query()
            ->where('is_current', '=', 1)
            ->whereIn('id', $this->getAllVersionIds())
            ->update(['is_current' => false, 'enabled' => false]);

        return $this;
    }

    /**
     * @return array
     */
    private function getAllVersionIds()
    {
        return self::query()
            ->where('key', $this->key)
            ->pluck('id')
            ->toArray();
    }

    /**
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        return $this->enabled && $this->is_current;
    }

    /**
     * @return bool
     */
    public function getCanPublishAttribute()
    {
        return $this->published_at ? $this->published_at->lt(Carbon::now()->addMinute()) : false;
    }

    /**
     * @return int
     */
    public function getNextVersionNumberAttribute()
    {
        return $this->highestVersionNumber + 1;
    }

    /**
     * @return int
     */
    public function getHighestVersionNumberAttribute()
    {
        return (int) self::query()
            ->where('key', 'like', $this->key . '%')
            ->max(
                'version'
            );
    }

    /**
     * @return bool
     */
    public function getIsHighestVersionAttribute()
    {
        return $this->version == $this->highestVersionNumber;
    }

    /**
     * @return ConsentOption
     */
    public function editableVersion()
    {
        return self::query()
            ->where('key', $this->key)
            ->where('version', $this->highestVersionNumber)
            ->first();
    }

    /**
     * @return string
     */
    public function getUserTypesBadgesAttribute()
    {
        $str = '';
        foreach ($this->models as $model) {
            $str .= "<span class='badge rounded-pill  badge-info bg-info'><i class='fa fa-user'></i> " . self::modelBasename(
                $model
            ) . '</span> ';
        }

        return trim($str);
    }

    /**
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active ? '<span class="btn btn-sm btn-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Active</span>' : ($this->is_current ? '<span class="btn btn-sm btn-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Disabled</span>' : '<span class="btn btn-sm btn-info">' . ($this->isHighestVersion ? 'draft' : 'locked') . '</span>');
    }

    /**
     * @return string
     */
    public function getRequiredBadgeAttribute()
    {
        return $this->is_mandatory ? '<span class="badge rounded-pill badge-success bg-success"><i class="fa fa-check-square" aria-hidden="true"></i> Mandatory</span>' : '<span class="badge rounded-pill badge-info bg-info"><i class="fa fa-question-circle" aria-hidden="true"></i> Optional</span>';
    }

    /**
     * @return string
     */
    public function getUsersAcceptedBadgeAttribute()
    {
        return '<span class="badge rounded-pill badge-success bg-success"><i class="fa fa-thumbs-up"></i> Accepted ' . $this->usersAcceptedTotal . '</span>';
    }

    /**
     * @return string
     */
    public function getUsersDeclinedBadgeAttribute()
    {
        return $this->is_mandatory ? '' : '<span class="badge rounded-pill badge-danger bg-danger ms-2"><i class="fa fa-thumbs-down"></i> Declined ' . $this->usersDeclinedTotal . '</span>';
    }

    /**
     * @return $this
     */
    public function toggleStatus()
    {
        $this->enabled = ! $this->enabled;

        return $this;
    }
}
