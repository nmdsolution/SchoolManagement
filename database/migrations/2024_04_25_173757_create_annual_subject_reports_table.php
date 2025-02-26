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
        Schema::create('annual_subject_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annual_report_id')->nullable()->references('id')->on('annual_reports')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedInteger('subject_total');
            $table->unsignedFloat('subject_avg');
            $table->unsignedInteger('subject_rank');
            $table->string("subject_grade");
            $table->string("subject_remarks");
            $table->string("term_marks");
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
        Schema::dropIfExists('annual_subject_reports');
    }
};
