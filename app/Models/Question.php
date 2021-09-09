<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id', 'title', 'option_type'];

    public function quiz() {
        return $this->belongsTo(Question::class, 'id');
    }

    public function options() {
        return $this->hasMany(Option::class, 'id');
    }
}
