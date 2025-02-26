<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('competency_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->foreignId('competency_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_term_id')->constrained('exam_terms')->onDelete('cascade');
            $table->foreignId('exam_sequence_id')->constrained('exam_sequences')->onDelete('cascade');
            $table->foreignId('session_year_id')->constrained()->onDelete('cascade');
            $table->decimal('obtained_marks', 5, 2);
            $table->boolean('passing_status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('competency_marks');
    }
}; 

