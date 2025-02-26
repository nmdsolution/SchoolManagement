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
        Schema::table('mediums', function (Blueprint $table) {
            $table->bigInteger('center_id')->nullable(true)->unsigned()->index()->after('name');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->bigInteger('center_id')->nullable(true)->unsigned()->index()->after('name');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->bigInteger('center_id')->nullable(true)->unsigned()->index()->after('type');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->unique(['name','type','center_id'],'name');
            $table->unique(['code','center_id'],'code');
        });
        Schema::table('classes', function (Blueprint $table) {
            $table->bigInteger('center_id')->nullable(true)->unsigned()->index()->after('medium_id');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mediums', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique('name');
            $table->dropUnique('code');
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');

        });
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
        });
    }
};
