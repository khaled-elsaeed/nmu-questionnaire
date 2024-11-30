<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'user_id',
    ];

     // A response belongs to a questionnaire
     public function questionnaire()
     {
         return $this->belongsTo(Questionnaire::class);
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

