<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTableStatusToJson extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->json('status')->nullable()->after('minisec_matricule');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('status')->default("Not applicable")->after('minisec_matricule');
        });
    }
}