<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'quiz_id', 'total', 'correct', 'incorrect', 'unattended', 'answers', 'created_at', 'updated_at'];
}
