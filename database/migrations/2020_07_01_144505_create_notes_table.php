<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
			$table->id();
			$table->integer('account_id');
            $table->integer('course_id');
            $table->integer('user_id');
            $table->integer('quiz_id');
            $table->integer('page_id');
            $table->integer('question_id');
            $table->longText('question_content');
            $table->integer('attempt_id');
            $table->text('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
