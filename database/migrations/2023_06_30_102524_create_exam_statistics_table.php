<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_statistics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('exam_id')->nullable(true)->unsigned()->index();
            $table->bigInteger('class_section_id')->nullable(true)->unsigned()->index();
            $table->integer('total_student')->nullable(true);
            $table->integer('total_attempt_student')->nullable(true);
            $table->integer('pass')->nullable(true);
            $table->bigInteger('session_year_id')->nullable(true)->unsigned()->index();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
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
        Schema::dropIfExists('exam_statistics');
    }
};
