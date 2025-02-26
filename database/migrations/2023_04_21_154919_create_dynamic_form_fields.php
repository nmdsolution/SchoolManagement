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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('type', 128)->comment('text,number,textarea,dropdown,checkbox,radio,fileupload');
            $table->tinyInteger('is_required')->default(0);
            $table->text('default_values')->comment('values of radio,checkbox,dropdown,etc')->nullable();
            $table->text('other')->comment('extra HTML attributes')->nullable();
            $table->foreignId('center_id')->nullable()->comment('Null = Added By Admin')->references('id')->on('centers')->onDelete('cascade');
            $table->integer('rank')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('students', static function (Blueprint $table) {
            $table->dropColumn('caste');
            $table->dropColumn('religion');
            $table->dropColumn('blood_group');
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->foreignId('center_id')->nullable(true)->references('id')->on('centers')->onDelete('cascade');
            $table->text('dynamic_field_values');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('form_fields');
        Schema::table('students', function (Blueprint $table) {
            $table->string('caste', 128);
            $table->string('religion', 128);
            $table->string('blood_group', 128);
            $table->string('height', 128);
            $table->string('weight', 128);
            $table->dropConstrainedForeignId('center_id');
            $table->dropColumn('dynamic_field_values');
        });
    }
};
