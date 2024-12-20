<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireTarget extends Model
{
    protected $fillable = [
        'questionnaire_id',
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
                                $subQuery->whereNull('program_id')
                                         ->orWhere('program_id', $studentDetails->program_id);
                            });
                  });
        });
    }

    public function scopeScopeWithActiveNotResponded(Builder $query, $userId): Builder
    {
        return $query->whereDoesntHave('responses', function ($subQuery) use ($userId) {
            $subQuery->where('user_id', $userId);
        })->where('end', '>', now());
    }

    public function scopeWithDeadlinePassedOrResponded(Builder $query, $userId): Builder
    {
        return $query->where(function ($query) use ($userId) {
            $query->whereHas('responses', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId);
            })->orWhere('end', '<', now());
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
