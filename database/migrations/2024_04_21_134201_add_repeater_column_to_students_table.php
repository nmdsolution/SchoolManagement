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
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('repeater')->default(false);
        });
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->boolean('repeater')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('repeater');
        });
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->dropColumn('repeater');
        });
    }
};
