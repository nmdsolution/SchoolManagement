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
        Schema::table('courses', static function (Blueprint $table) {
            $table->tinyText('duration')->comment('in Hours')->change();
            $table->string('thumbnail', 512)->after('duration');
            $table->text('description')->nullable()->change();
            $table->string('tags', 512)->after('description')->nullable();
            $table->dropColumn('code');
        });

        Schema::create('course_sections', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->string('title', 512);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('course_material', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->references('id')->on('course_sections')->onDelete('cascade');
            $table->tinyText('type')->comment('File , Video');
            $table->string('url', 1024);
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
        Schema::table('courses', static function (Blueprint $table) {
            $table->string('duration')->comment('in Hours')->change();
            $table->dropColumn('thumbnail');
            $table->string('description')->change();
            $table->dropColumn('tags')->after('description');
            $table->string('code', 64)->nullable();
        });

        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('course_material');
    }
};
