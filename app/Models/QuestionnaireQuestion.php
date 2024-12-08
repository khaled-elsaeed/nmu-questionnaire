<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class QuestionnaireQuestion extends Pivot
{
    protected $fillable = ['questionnaire_id', 'question_id', 'display_order', 'is_mandatory'];
}
