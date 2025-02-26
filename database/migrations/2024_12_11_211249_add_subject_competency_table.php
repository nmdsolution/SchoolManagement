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
        Schema::create('subject_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_sequence_id')->constrained('exam_sequences')->onDelete('cascade'); 
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade'); 
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); 
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade'); 
            $table->text('competence'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_competencies');
    }
};
