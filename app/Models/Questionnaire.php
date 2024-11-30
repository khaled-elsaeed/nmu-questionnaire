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

   // A questionnaire has many questions through the questionnaire_questions pivot table
   public function questions()
   {
       return $this->belongsToMany(Question::class, 'questionnaire_questions')
                   ->withPivot('display_order', 'is_mandatory')
                   ->orderBy('pivot_display_order'); // Ordering by display_order in the pivot table
   }

   // A questionnaire has many responses
   public function responses()
   {
       return $this->hasMany(Response::class);
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




}
