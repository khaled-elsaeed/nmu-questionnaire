<?php   

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    protected $fillable = [
        'user_id',
        'faculty_id',
        'department_id',
        'program_id',
        'level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
