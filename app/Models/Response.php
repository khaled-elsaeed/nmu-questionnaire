<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


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

    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Check if a user has responded to a specific questionnaire target
    public static function hasUserResponded($userId, $targetId)
    {
        // Check if a response exists for the given user and target
        return self::where('user_id', $userId)
                   ->where('questionnaire_target_id', $targetId)
                   ->exists();
    }
}


