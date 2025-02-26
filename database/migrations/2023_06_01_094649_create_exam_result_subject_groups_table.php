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
        Schema::create('exam_result_groups', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_result_group_subjects', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('exam_result_group_id')->nullable()->references('id')->on('exam_result_groups')->onDelete('cascade');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('exam_marks', static function (Blueprint $table) {
            $table->foreignId('exam_result_group_id')->after('grade')->references('id')->on('exam_result_groups')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_result_group_subjects');
        Schema::table('exam_marks', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('exam_result_group_id');
        });
        Schema::dropIfExists('exam_result_groups');
    }
};
