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
        //
        Schema::table('fees_types', function (Blueprint $table) {
            $table->bigInteger('center_id')->nullable(true)->unsigned()->index()->after('choiceable');
            $table->bigInteger('medium_id')->nullable(true)->unsigned()->index()->after('center_id');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('fees_types', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');

            $table->dropForeign(['medium_id']);
            $table->dropColumn('medium_id');
        });
    }
};
