<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

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
    return $this->hasMany(Response::class);
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

public function scopeForRole(Builder $query, string $role): Builder
    {
        return $query->where('role_name', $role);
    }

    public function scopeForGlobalOrLocalScope(Builder $query, $studentDetails): Builder
    {
        return $query->where(function ($query) use ($studentDetails) {
            $query->where('scope_type', 'global')
                  ->orWhere(function ($query) use ($studentDetails) {
                      $query->where('scope_type', 'local')
                            ->where(function ($subQuery) use ($studentDetails) {
                                $subQuery->whereNull('faculty_id')
                                         ->orWhere('faculty_id', $studentDetails->faculty_id);
                            })
                            ->where(function ($subQuery) use ($studentDetails) {
                                $subQuery->whereNull('dept_id')
                                         ->orWhere('dept_id', $studentDetails->department_id);
                            })
                            ->where(function ($subQuery) use ($studentDetails) {
                                $subQuery->whereNull('program_id')
                                         ->orWhere('program_id', $studentDetails->program_id);
                            });
                  });
        });
    }

    public function scopeScopeWithActiveNotResponded(Builder $query, $userId): Builder
    {
        return $query->where(function ($query) use ($userId) {
            $query->whereDoesntHave('responses', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId); // Ensure the user hasn't responded
            })
            ->whereHas('questionnaire', function ($subQuery) {
                $subQuery->where('end_date', '>', now()); // Ensure the questionnaire is active (end_date is in the future)
            });
        });
    }
    

    public function scopeWithDeadlinePassedOrResponded(Builder $query, $userId): Builder
    {
        return $query->where(function ($query) use ($userId) {
            $query->whereHas('responses', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId); // Ensure the user has responded
            })
            ->orWhereHas('questionnaire', function ($subQuery) {
                $subQuery->where('end_date', '<', now()); // Ensure the questionnaire's deadline has passed
            });
        });
    }
    


    public function scopeForCourses(Builder $query, $studentDetails): Builder
    {
        return $query->where(function ($query) use ($studentDetails) {
            $query->whereNull('course_detail_id')
                  ->orWhereHas('courseEnrollments', function ($subQuery) use ($studentDetails) {
                      $subQuery->where('student_id', $studentDetails->id);
                  });
        });
    }

}
