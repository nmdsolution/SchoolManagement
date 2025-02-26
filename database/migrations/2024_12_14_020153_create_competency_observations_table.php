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
        Schema::create('competency_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_term_id')->constrained('exam_terms');
            $table->string('teacher_signature')->nullable();
            $table->string('director_signature')->nullable();
            $table->string('parent_signature')->nullable();
            $table->text('observation');
            $table->timestamps();
        });

        Schema::create('council_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('review')->nullable(); // Texte de l'avis du conseil des maîtres
            $table->foreignId('competency_observation_id')->constrained('competency_observations');
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
        Schema::dropIfExists('council_reviews');
        Schema::dropIfExists('competency_observations');
    }
};
