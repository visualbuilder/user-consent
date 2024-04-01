<?php

namespace Visualbuilder\FilamentUserConsent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $consent_option_id
 * @property int $consentable_id
 * @property int $consent_option_question_id
 * @property string $question_field_name
 * @property string $response
 * @property string $additional_info
 * @property string $created_at
 * @property string $updated_at
 */
class ConsentableResponse extends Model
{
    public $incrementing = true;

    protected $table = 'consentable_responses';

    protected $fillable = [
        'consentable_id',
        'consent_option_id',
        'consent_option_question_id',
        'question_field_name',
        'response',
        'additional_info'
    ];

    public function consent(): BelongsTo
    {
        return $this->belongsTo(ConsentOptionUser::class);
    }
}
