<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireTarget extends Model
{
    protected $fillable = [
        'questionnaire_id',
        'dept_id',
        'program_id',
        'faculty_id',
        'role_name',
        'level',
        'scope_type',
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
