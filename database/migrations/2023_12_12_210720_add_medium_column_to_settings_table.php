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
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedBigInteger("medium_id")->nullable();
            $table->foreign("medium_id")->references('id')->on('mediums');

            $table->dropUnique(['type', 'center_id']);
            $table->unique(['type', 'center_id', 'medium_id'], 'type');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->unsignedBigInteger("medium_id")->nullable();
            $table->foreign("medium_id")->references('id')->on('mediums');          
        });

        Schema::table('exam_terms', function (Blueprint $table) {
            $table->unsignedBigInteger("medium_id")->nullable();
            $table->foreign("medium_id")->references('id')->on('mediums');          
        });

        Schema::table('effective_domains', function (Blueprint $table) {
            $table->unsignedBigInteger("medium_id")->nullable();
            $table->foreign("medium_id")->references('id')->on('mediums');

            $table->dropUnique(['name', 'center_id']);
            $table->unique(['name', 'center_id', 'medium_id'], 'name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('type');
            $table->dropConstrainedForeignId('medium_id');

            $table->unique(['type', 'center_id'], 'type');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropConstrainedForeignId("medium_id");
        });

        Schema::table('exam_terms', function (Blueprint $table) {
            $table->dropConstrainedForeignId("medium_id");
        });

        Schema::table('effective_domains', function (Blueprint $table) {
            $table->dropUnique('name');
            $table->dropConstrainedForeignId('medium_id');

            $table->unique(['name', 'center_id'], 'name');
        });
    }
};
