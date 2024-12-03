<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
   // CourseEnrollment.php
public function studentDetail()
{
    return $this->belongsTo(StudentDetail::class);
}

public function courseDetail()
{
    return $this->belongsTo(CourseDetail::class);
}

}
