<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLsaResultComparisonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lsa_result_comparisons', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id');
            $table->integer('course_id');
            $table->integer('question_id');
            $table->integer('page_id');
            $table->longText('question_content');
            $table->longText('page_content');
            $table->float('params');
            $table->integer('status');
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
        Schema::dropIfExists('lsa_result_comparisons');
    }
}
