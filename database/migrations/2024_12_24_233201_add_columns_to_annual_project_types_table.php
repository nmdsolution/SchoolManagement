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
        Schema::table('annual_project_types', function (Blueprint $table) {
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('session_year_id')->constrained('session_years')->onDelete('cascade');
            $table->foreignId('medium_id')->constrained('mediums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('annual_project_types', function (Blueprint $table) {
            $table->dropForeign(['center_id', 'session_year_id', 'medium_id']);
            $table->dropColumn(['center_id', 'session_year_id', 'medium_id']);
        });
    }
};
