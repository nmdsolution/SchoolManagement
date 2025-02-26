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
        Schema::table('exam_class_sections', static function (Blueprint $table) {
            $table->tinyInteger('publish')->default(0)->comment('0 => No, 1 => Yes')->after('class_section_id');
        });

        Schema::table('exams', static function (Blueprint $table) {
            $table->dropColumn('publish');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_class_sections', static function (Blueprint $table) {
            $table->dropColumn('publish');
        });
        Schema::table('exams', static function (Blueprint $table) {
            $table->tinyInteger('publish')->default(0)->comment('0 => No, 1 => Yes')->after('exam_sequence_id');
        });
    }
};
