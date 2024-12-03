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
        'course_detail_id',
        'role_name',
        'level',
        'scope_type',
    ];

  // QuestionnaireTarget.php
// In App\Models\QuestionnaireTarget.php

public function responses()
{
    return $this->hasMany(Response::class, 'questionnaire_target_id');
}

public function Questionnaire()
{
    return $this->belongsTo(Questionnaire::class);
}

public function courseEnrollments()
{
    return $this->hasMany(CourseEnrollment::class, 'course_detail_id', 'course_detail_id');
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
