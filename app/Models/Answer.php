<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'question_id',
        'option_id',
        'answer_text',
    ];


     // An answer belongs to a response
     public function response()
     {
         return $this->belongsTo(Response::class);
     }
 
     // An answer belongs to a question
     public function question()
     {
         return $this->belongsTo(Question::class);
     }
 
     // An answer can belong to an option (if it's a multiple choice answer)
     public function option()
     {
         return $this->belongsTo(Option::class);
     }
}
