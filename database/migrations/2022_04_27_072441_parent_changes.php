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
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender', 16)->nullable()->after('name');
            $table->string('current_address')->nullable()->after('dob');
            $table->string('permanent_address')->nullable()->after('current_address');

            $table->dropColumn('name');
            $table->string('first_name', 128)->nullable(true)->after('id');
            $table->string('last_name', 128)->nullable(true)->after('first_name');


        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('father_id')->nullable()->after('is_new_admission')->references('id')->on('parents')->onDelete('cascade');
            $table->foreignId('mother_id')->nullable()->after('father_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreignId('guardian_id')->nullable()->after('mother_id')->references('id')->on('parents')->onDelete('cascade');

            $table->dropColumn('father_name');
            $table->dropColumn('father_phone');
            $table->dropColumn('father_occupation');
            $table->dropColumn('father_image');
            $table->dropColumn('mother_name');
            $table->dropColumn('mother_occupation');
            $table->dropColumn('mother_image');
            $table->dropColumn('parent_id');
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->string('first_name', 128)->nullable(true)->after('user_id');
            $table->string('last_name', 128)->nullable(true)->after('first_name');
            $table->string('gender', 16)->after('last_name');
            $table->string('email')->nullable()->after('gender');
            $table->string('mobile', 16)->nullable(true)->after('email');
            $table->string('occupation', 128)->nullable(true)->after('mobile');
            $table->string('image', 1024)->nullable()->after('occupation');
            $table->date('dob')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('current_address');
            $table->dropColumn('permanent_address');

            $table->string('name');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('father_id');
            $table->dropConstrainedForeignId('mother_id');
            $table->dropConstrainedForeignId('guardian_id');
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('gender');
            $table->dropColumn('email');
            $table->dropColumn('mobile');
            $table->dropColumn('occupation');
            $table->dropColumn('image');
            $table->dropColumn('dob');
        });
    }
};
