<?php

namespace Tests\Unit;

use App\Http\Controllers\QuizController;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    public function tearDown(): void
    {
        DB::rollBack();
    }

    /**
     * Unit test to validate the updateNotification method
     *
     * @return void
     */
    public function test_update_notification_status()
    {
        $quiz = Quiz::get()->random();

        $notificationStatus = $quiz->notification_status;
        $newNotificationStatus = $notificationStatus === 'on' ? 'off' : 'on';

        $request = new Request([
            'notification_status'   => $newNotificationStatus,
        ]);

        $request->setMethod('POST');

        $response = (new QuizController())->updateNotification($request, $quiz);

        $this->assertEquals(302, $response->status());

        $quiz = $quiz->fresh();

        $this->assertEquals($newNotificationStatus, $quiz->notification_status);
    }

    /**
     * Unit test to validate the update method
     *
     * @return void
     */
    public function test_update()
    {
        $quiz = Quiz::get()->random();

        $newTitle = strrev($quiz->title);
        $newDuration = $quiz->duration + 60;
        $notificationStatus = $quiz->notification_status;
        $newNotificationStatus = $notificationStatus === 'on' ? 'off' : 'on';

        $request = new Request([
            'title'                 => $newTitle,
            'duration'              => $newDuration,
            'notification_status'   => $newNotificationStatus,
        ]);

        $request->setMethod('POST');

        $response = (new QuizController())->update($request, $quiz,
            ['title', 'duration', 'notification_status']);

        $this->assertEquals(302, $response->status());

        $quiz = $quiz->fresh();

        $this->assertEquals($newTitle, $quiz->title);
        $this->assertEquals($newDuration, $quiz->duration);
        $this->assertEquals($newNotificationStatus, $quiz->notification_status);
    }
}
