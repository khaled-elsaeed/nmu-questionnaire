<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'questionnaire_questions')
                    ->withPivot('display_order', 'is_mandatory')
                    ->withTimestamps();
    }

// Questionnaire.php
public function questionnaireTargets()
{
    return $this->hasMany(QuestionnaireTarget::class);
}

public function courseDetails()
{
    return $this->belongsToMany(CourseDetail::class, 'questionnaire_targets');
}


public function responses()
{
    return $this->hasMany(Response::class);
}


}
