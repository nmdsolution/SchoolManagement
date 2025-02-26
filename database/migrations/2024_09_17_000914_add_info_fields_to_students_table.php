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
        // drop the already existing status first.
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('minisec_matricule')->nullable()->after('admission_date');
            $table->string('status')->default("Not applicable")->after('minisec_matricule');
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
            $table->dropColumn('minisec_matricule');
            $table->dropColumn('status');
        });
    }
};
