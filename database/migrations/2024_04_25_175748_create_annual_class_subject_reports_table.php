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
        Schema::create('annual_class_subject_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annual_report_id')->nullable()->references('id')->on('annual_reports')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->float('min');
            $table->float('max');
            $table->timestamps();
            $table->unique(['annual_report_id', 'subject_id'], 'unique_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('annual_class_subject_reports');
    }
};
