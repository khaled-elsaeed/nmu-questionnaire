<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['text', 'type', 'question_module_id']; // Ensure you include the foreign key
    
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class, 'questionnaire_questions')
                    ->withPivot('display_order', 'is_mandatory')
                    ->withTimestamps();
    }
}

