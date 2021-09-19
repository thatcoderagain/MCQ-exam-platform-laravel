<?php

namespace App\Console\Commands;

use App\Http\Controllers\MailController;
use App\Models\Quiz;
use Illuminate\Console\Command;

class DailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailyDigest:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command send an email to quiz authors everyday at 10:00 AM with the test summary of users in last 24 hours.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            echo "Program started\n";
            $quizAuthors = Quiz::dailyDigest();
            foreach ($quizAuthors as $author) {

                $authorName = $author['name'];
                $email = $author['email'];
                $quizzes = $author['quizzes'];

                foreach ($quizzes as $quiz) {
                    $quizTitle = $quiz['title'];
                    $attempts = $quiz['attempts'];

                    if (count($attempts)) {
                        MailController::sendMail($email, 'emails.dailyDigest',
                            compact('authorName', 'quizTitle', 'attempts'), 'Daily Digest');
                        echo "Email sent to $email\n";
                    }
                }
            }
            echo "Program stopped\n";
            return 0;
        } catch (\Exception $e) {
            var_dump('Error occurred: '.$e->getMessage());
            return 1;
        }
    }
}
