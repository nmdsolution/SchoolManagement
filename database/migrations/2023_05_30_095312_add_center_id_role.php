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
        Schema::table('roles', function (Blueprint $table) {
            $table->bigInteger('center_id')->nullable(true)->unsigned()->index()->comment('Super admin roles if NULL')->after('is_default');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->dropUnique('roles_name_guard_name_unique');
            // $table->unique(['name', 'guard_name','center_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
            // $table->dropUnique('roles_name_guard_name_center_id_unique');
        });
    }
};
