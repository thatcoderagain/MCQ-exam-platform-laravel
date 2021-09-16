<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->smallInteger('total');
            $table->smallInteger('correct');
            $table->smallInteger('incorrect');
            $table->smallInteger('unattended');
            $table->json('answers');
            $table->timestamps();

            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('quiz_id')->on('quizzes')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
