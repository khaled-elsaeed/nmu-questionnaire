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

    /**
     * Get the answers associated with this response.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get the questionnaire associated with this response.
     */
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    /**
     * Get the user who submitted the response.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

