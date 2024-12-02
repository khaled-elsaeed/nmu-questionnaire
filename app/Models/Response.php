<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_target_id',
        'user_id',
    ];

     // In App\Models\Response.php

public function questionnaireTarget()
{
    return $this->belongsTo(QuestionnaireTarget::class, 'questionnaire_target_id');
}

     
 
     // A response can have many answers
     public function answers()
     {
         return $this->hasMany(Answer::class);
     }

    // A response belongs to a question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }


    /**
     * Get the user who submitted the response.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

