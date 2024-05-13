<?php

namespace Visualbuilder\FilamentUserConsent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Visualbuilder\FilamentUserConsent\Database\Factories\ConsentOptionQuestionFactory;

class ConsentOptionQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consent_option_id',
        'component',
        'name',
        'label',
        'icon',
        'required',
        'sort',
        'content',
        'group_number',
        'default_user_column'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'consent_option_id' => 'integer',
        'required' => 'boolean'
    ];

    /**
     * @return ConsentOptionQuestionFactory
     */
    protected static function newFactory(): ConsentOptionQuestionFactory
    {
        return ConsentOptionQuestionFactory::new();
    }

    public function options(): HasMany
    {
        return $this->hasMany(ConsentOptionQuestionOption::class);
    }

    public function additionalInfoOptions(): HasMany
    {
        return $this->hasMany(ConsentOptionQuestionOption::class)->where('additional_info', true);
    }

    public function consentOption(): BelongsTo
    {
        return $this->belongsTo(ConsentOption::class);
    }
}
