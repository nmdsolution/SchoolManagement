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
        // Schema::table('students', function (Blueprint $table) {
        //     $table->bigInteger('center_id')->nullable(true)->unsigned()->index()->after('guardian_id');
        //     $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade')->onUpdate('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('students', function (Blueprint $table) {
        //     //
        //     $table->dropForeign(['center_id']);
        //     $table->dropColumn('center_id');

        // });
    }
};
