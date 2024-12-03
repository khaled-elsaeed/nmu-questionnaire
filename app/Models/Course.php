<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function courseDetails()
    {
        return $this->hasMany(CourseDetail::class, 'course_id');
    }


}
