<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'duration', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function getQuestion($questionNumber, $columns = []){
        $question = self::questions();
            if (count($columns)) {
                $question = $question->select($columns);
            }
        return $question->offset($questionNumber)
            ->firstOrFail();
    }
}
