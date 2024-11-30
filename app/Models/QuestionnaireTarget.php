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

  // QuestionnaireTarget.php
public function questionnaire()
{
    return $this->belongsTo(Questionnaire::class);
}

public function courseDetail()
{
    return $this->belongsTo(CourseDetail::class);
}

public function department()
{
    return $this->belongsTo(Department::class);
}

public function program()
{
    return $this->belongsTo(Program::class);
}

public function faculty()
{
    return $this->belongsTo(Faculty::class);
}

}
