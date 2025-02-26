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
        Schema::table('exam_sequences', static function (Blueprint $table) {
            $table->date('start_date')->after('exam_term_id')->nullable();
            $table->date('end_date')->after('start_date')->nullable();
            $table->tinyInteger('status')->after('end_date')->default(0);
        });

        Schema::table('exams', static function (Blueprint $table) {
            $table->tinyInteger('teacher_status')->after('exam_sequence_id')->default(0);
            $table->tinyInteger('student_status')->after('teacher_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('exam_sequences', static function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('status');
        });

        Schema::table('exam_sequences', static function (Blueprint $table) {
            $table->dropColumn('teacher_status');
            $table->dropColumn('student_status');
        });
    }
};
