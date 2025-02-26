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
    public function up() {
        Schema::create('mediums', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('qualification', 512)->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreignId('class_teacher_id')->nullable()->references('id')->on('teachers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('session_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->tinyInteger('default')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->string('code', 64)->nullable();
            $table->string('bg_color', 32);
            $table->string('image', 512);
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->string('type', 64)->comment('Theory / Practical');
            // $table->unique(['name','type','center_id'],'name');
            // $table->unique(['code','center_id'],'code');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->string('admission_no', 512);
            $table->integer('roll_number');
            $table->string('caste', 128)->nullable();
            $table->string('religion', 128)->nullable();
            $table->date('admission_date');
            $table->string('blood_group', 32)->nullable();
            $table->string('height', 32)->nullable();
            $table->string('weight', 64)->nullable();
            $table->string('father_name', 128)->nullable();
            $table->string('father_phone', 64)->nullable();
            $table->string('father_occupation', 128)->nullable();
            $table->string('father_image', 512)->nullable();
            $table->string('mother_name', 128)->nullable();
            $table->string('mother_occupation', 128)->nullable();
            $table->string('mother_image', 512)->nullable();
            $table->integer('parent_id');
            $table->tinyInteger('is_new_admission')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->string('type', 32)->comment('Compulsory / Elective');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->integer('elective_subject_group_id')->nullable()->comment('if type=Elective');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('elective_subject_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('total_subjects');
            $table->integer('total_selectable_subjects');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subject_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->string('description', 1024)->nullable();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lesson_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->string('name', 128);
            $table->string('description', 1024)->nullable();
            $table->string('file', 512)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->string('name', 128);
            $table->string('instructions', 1024)->nullable();
            $table->dateTime('due_date');
            $table->integer('points')->nullable();
            $table->boolean('resubmission')->default(0);
            $table->integer('extra_days_for_resubmission')->nullable();
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->text('feedback')->nullable();
            $table->integer('points')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = Pending/In Review , 1 = Accepted , 2 = Rejected , 3 = Resubmitted');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('description', 1024)->nullable();
            $table->integer('class_id');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyInteger('publish')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->integer('total_marks');
            $table->integer('passing_marks');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_timetable_id')->references('id')->on('exam_timetables')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->float('obtained_marks');
            $table->string('teacher_review', 1024)->nullable();
            $table->boolean('passing_status')->comment('1=Pass, 0=Fail');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyText('grade')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->integer('total_marks');
            $table->integer('obtained_marks');
            $table->float('percentage');
            $table->tinyText('grade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->unique(['exam_id', 'class_section_id', 'student_id'], 'unique_id');
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->integer('minimum_percentage');
            $table->integer('maximum_percentage');
            $table->tinyText('grade');
            $table->timestamps();
        });

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_teacher_id')->references('id')->on('subject_teachers')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('note', 1024)->nullable();
            $table->integer('day')->comment('1=monday,2=tuesday,3=wednesday,4=thursday,5=friday,6=saturday,7=sunday');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('description', 1024)->nullable();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->float('amount');
            $table->date('due_date');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fees_sub_types', function (Blueprint $table) {
            $table->id();
            $table->integer('fees_id');
            $table->foreignId('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->string('name');
            $table->string('description', 1024)->nullable();
            $table->float('amount');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('fees_paid', function (Blueprint $table) {
            $table->id();
            $table->integer('fees_id');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->tinyInteger('status')->default(0)->comment('0=Not Paid,1=Paid');
            $table->string('description', 1024)->nullable();
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 128)->nullable(true);
            $table->string('description', 1024)->nullable(true);
            $table->nullableMorphs('table');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('title', 512);
            $table->string('description', 1024)->nullable();
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyInteger('type')->comment('0=Absent, 1=Present');
            $table->date('date');
            $table->string('remark', 512);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('categories');
        Schema::dropIfExists('session_years');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('mediums');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('elective_subject_groups');
        Schema::dropIfExists('subject_teachers');
        Schema::dropIfExists('student_subjects');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('lesson_topics');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_timetables');
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('fees');
        Schema::dropIfExists('fees_sub_types');
        Schema::dropIfExists('fees_paid');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('academic_calendars');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
    }
};
