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
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->time('start_time',0);
            $table->time('end_time',0);
            $table->integer('status')->default(0);
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('stream_id')->nullable()->after('medium_id')->references('id')->on('streams')->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->after('stream_id')->references('id')->on('shifts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_id');
            $table->dropConstrainedForeignId('stream_id');
        });
        Schema::dropIfExists('streams');
        Schema::dropIfExists('shifts');
    }
};
