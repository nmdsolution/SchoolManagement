<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_reports', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_teacher_id')->nullable()->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('exam_term_id')->references('id')->on('exam_terms')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->float('avg');
            $table->integer('total_coef');
            $table->integer('total_points');
            $table->timestamps();
        });


        Schema::create('exam_report_class_details', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_report_id')->references('id')->on('exam_reports')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->float('total_obtained_points')->nullable();
            $table->float('avg')->nullable();
            $table->integer('rank')->nullable();
            $table->timestamps();
        });

        Schema::table('grades', static function (Blueprint $table) {
            $table->tinyText('remarks')->nullable()->after('center_id');
        });

        Schema::create('effective_domains', static function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->unique(['name', 'center_id']);
            $table->timestamps();
        });

        Schema::create('exam_report_student_subjects', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_report_id')->references('id')->on('exam_reports')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->integer('subject_total');
            $table->integer('subject_rank');
            $table->float('subject_avg');
            $table->tinyText('subject_grade')->nullable();
            $table->tinyText('subject_remarks')->nullable();
            $table->text('sequence_marks')->nullable();
            $table->unique(['exam_report_id', 'student_id', 'subject_id'], 'unique_ids');
            $table->timestamps();
        });

        Schema::create('exam_report_class_subjects', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_report_id')->references('id')->on('exam_reports')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->float('min');
            $table->float('max');
            $table->unique(['exam_report_id', 'subject_id'], 'unique_ids');
            $table->timestamps();
        });

        Schema::create('exam_report_student_sequences', static function (Blueprint $table) {
            $table->id();
            //            $table->foreignId('exam_report_id')->references('id')->on('exam_reports')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('exam_term_id')->references('id')->on('exam_terms')->onDelete('cascade');
            $table->foreignId('exam_sequence_id')->references('id')->on('exam_sequences')->onDelete('cascade');
            $table->float('total');
            $table->float('avg');
            $table->integer('rank');
            $table->unique(['class_section_id', 'student_id', 'exam_term_id', 'exam_sequence_id'], 'unique_ids');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('exam_reports');
        Schema::dropIfExists('exam_report_class_details');
        Schema::table('grades', static function (Blueprint $table) {
            $table->dropColumn('remarks');
        });

        Schema::dropIfExists('effective_domains');
        Schema::table('exam_report_details', static function (Blueprint $table) {
            $table->text('subject_wise_details');
        });
        Schema::dropIfExists('exam_report_student_subjects');
        Schema::dropIfExists('exam_report_class_subjects');
        Schema::dropIfExists('exam_report_student_sequences');
    }
};
