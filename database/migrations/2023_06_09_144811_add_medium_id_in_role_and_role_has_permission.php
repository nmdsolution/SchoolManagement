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
            $table->bigInteger('medium_id')->nullable(true)->unsigned()->index()->after('center_id');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->unique(['name', 'guard_name','center_id','medium_id'],'role_name');
        });
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->bigInteger('medium_id')->nullable(true)->unsigned()->index()->after('role_id');
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
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['medium_id']);
            $table->dropColumn('medium_id');
            $table->dropUnique('role_name');
        });
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign(['medium_id']);
            $table->dropColumn('medium_id');
        });
        
    }
};
