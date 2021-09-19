<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function test_user_gets_redirected_to_quiz_list()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
