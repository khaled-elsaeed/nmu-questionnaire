<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    // CourseDetail.php
// Course.php
public function courseDetails()
{
    return $this->hasMany(CourseDetail::class);
}


}
