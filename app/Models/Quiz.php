<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function attempts() {
        return $this->hasMany(Test::class);
    }


    public static function dailyDigest() {
        return User::get()->map(function ($user) {
            $user['quizzes'] = $user->quizzes()
                ->where('notification_status', 'on')->get()
                ->map(function ($quiz) {
                    $quiz['attempts'] = $quiz->attempts()
                        ->where('updated_at', '>', Carbon::now()->subHours(24))
                        ->groupBY('user_id')
                        ->select([
                            'user_id',
                            DB::raw('COUNT(id) as total_attempts'),
                            DB::raw('(SUM(correct) / SUM(total)) * 100 as average_score'),
                            DB::raw('MAX(correct) as max_score'),
                            DB::raw('MIN(correct) as min_score'),
                        ])
                        ->get()
                        ->map(function ($test) {
                            $test['username'] = $test->user->name;
                            return $test->only(['username', 'total_attempts', 'average_score', 'max_score', 'min_score']);
                        });
                    return $quiz->only(['title', 'attempts']);
                });
            return $user->only(['name', 'email', 'quizzes']);
        });
    }
}
