<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('student'); // student or staff
            $table->string('page_layout')->default('a4_landscape');
            $table->integer('height')->default(210);
            $table->integer('width')->default(297);
            $table->string('user_image_shape')->nullable();
            $table->string('image_size')->nullable();
            $table->string('certificate_title');
            $table->string('certificate_heading');
            $table->longText('certificate_text');
            $table->string('background_image')->nullable();
            $table->string('background_image_path')->nullable();
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('center_id')->nullable();
            $table->timestamps();

            $table->foreign('center_id')->references('id')->on('centers')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
