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
        Schema::table('center_teachers', function (Blueprint $table) {
            //
            $table->string('manage_student_parent')->default(0)->comment('0 => No permission, 1 => Permission')->after('user_id');
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
        Schema::table('center_teachers', static function (Blueprint $table) {
            $table->dropColumn('manage_student_parent');
        });
    }
};
