<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['text', 'type', 'question_module_id']; // Ensure you include the foreign key
    
    // A question belongs to a question module
    public function module()
    {
        return $this->belongsTo(QuestionModule::class, 'question_module_id');
    }

    // A question can have many options (for multiple choice questions)
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    // A question can have many answers
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class, 'questionnaire_questions')
                    ->withPivot('display_order', 'is_mandatory')
                    ->withTimestamps();
    }

     
 
    
}

