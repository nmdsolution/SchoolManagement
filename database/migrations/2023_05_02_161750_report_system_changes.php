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

       Schema::table('class_subjects', static function (Blueprint $table) {
           $table->integer('weightage')->default(0)->after('subject_id');
       });

       Schema::create('exam_terms', static function (Blueprint $table) {
           $table->id();
           $table->string('name', 128);
           $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
           $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
           $table->timestamps();
       });


       Schema::create('exam_marks_status', static function (Blueprint $table) {
           $table->id();
           $table->foreignId('exam_id')->references('id')->on('exams')->onDelete('cascade');
           $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
           $table->foreignId('teacher_id')->nullable()->references('id')->on('teachers')->onDelete('cascade');
           $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
           $table->tinyInteger('status')->default(0)->comment('0 = Pending, 1 = Submitted ,2 = In Progress');
           $table->timestamps();
       });
       Schema::create('exam_sequences', static function (Blueprint $table) {
           $table->id();
           $table->string('name', 512);
           $table->foreignId('exam_term_id')->references('id')->on('exam_terms')->onDelete('cascade');
           $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
           $table->timestamps();
       });

        Schema::dropIfExists('exam_classes');
        // Schema::create('exam_class_sections', static function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('exam_id')->references('id')->on('exams')->onDelete('cascade');
        //     $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
        //     $table->timestamps();
        // });

        Schema::table('exam_class_sections', static function (Blueprint $table) {
            $table->bigInteger('class_section_id')->nullable(true)->unsigned()->index()->after('class_id');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
        });


       Schema::table('exams', static function (Blueprint $table) {
           $table->tinyInteger('type')->after('description')->comment('1 = Sequential Exam, 2 = Specific Exam');
           $table->foreignId('exam_term_id')->nullable()->after('session_year_id')->references('id')->on('exam_terms')->onDelete('cascade');
           $table->foreignId('exam_sequence_id')->nullable()->after('exam_term_id')->references('id')->on('exam_sequences')->onDelete('cascade');
           $table->foreignId('center_id')->after('exam_term_id')->references('id')->on('centers')->onDelete('cascade');
       });


        Schema::table('exam_timetables', static function (Blueprint $table) {
            $table->tinyInteger('marks_upload_status')->default(0)->after('session_year_id')->comment('0 = Pending , 1 = Submitted , 2 = In progress');
            $table->bigInteger('class_section_id')->nullable(true)->unsigned()->index()->after('marks_upload_status');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

       Schema::table('class_subjects', static function (Blueprint $table) {
           $table->dropColumn('weightage');
       });

       Schema::dropIfExists('exam_terms');
       Schema::dropIfExists('exam_sequences');
       Schema::dropIfExists('exam_marks_status');

       

       Schema::table('exams', static function (Blueprint $table) {
           $table->dropConstrainedForeignId('exam_term_id');
           $table->dropConstrainedForeignId('exam_sequence_id');
           $table->dropConstrainedForeignId('center_id');
           $table->dropColumn('center_id');
       });


        Schema::table('exam_timetables', static function (Blueprint $table) {
            $table->dropColumn('marks_upload_status');
            $table->dropForeign(['class_section_id']);
            $table->dropColumn('class_section_id');
        });

        Schema::table('exam_class_sections', static function (Blueprint $table) {
            $table->dropForeign(['class_section_id']);
            $table->dropColumn('class_section_id');
        });
    }
};
