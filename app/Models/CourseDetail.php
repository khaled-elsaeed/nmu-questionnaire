<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
// CourseDetail.php
public function course()
{
    return $this->belongsTo(Course::class);
}

public function questionnaireTargets()
{
    return $this->hasMany(QuestionnaireTarget::class);
}

public function courseEnrollments()
{
    return $this->hasMany(CourseEnrollment::class);
}


}
