<?php

namespace Visualbuilder\FilamentUserConsent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Visualbuilder\FilamentUserConsent\Database\Factories\ConsentOptionQuestionOptionFactory;

class ConsentOptionQuestionOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consent_option_question_id',
        'value',
        'text',
        'sort',
        'additional_info',
        'additional_info_label'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'consent_option_question_id' => 'integer',
        'additional_info' => 'bool'
    ];

     /**
     * @return ConsentOptionQuestionOptionFactory
     */
    protected static function newFactory(): ConsentOptionQuestionOptionFactory
    {
        return ConsentOptionQuestionOptionFactory::new();
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ConsentOptionQuestion::class);
    }
}
