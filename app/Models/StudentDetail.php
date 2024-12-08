<?php   

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    protected $fillable = [
        'user_id',
        'faculty_id',
        'program_id',
        'level',
    ];

    // StudentDetail.php
public function user()
{
    return $this->belongsTo(User::class);
}

public function faculty()
{
    return $this->belongsTo(Faculty::class);
}



public function program()
{
    return $this->belongsTo(Program::class);
}

}
