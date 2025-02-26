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
        Schema::create('class_annual_project_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annual_project_id')->constrained('annual_projects')->onDelete('cascade');
            $table->foreignId('exam_sequence_id')->constrained('exam_sequences')->onDelete('cascade');
            $table->foreignId('annual_project_type_id')->constrained('annual_project_types')->onDelete('cascade');
            $table->integer('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_annual_project_types');
    }
};
