<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_competency_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_competency_id')->constrained('class_competency')->onDelete('cascade');
            $table->foreignId('competency_type_id')->constrained('competency_types')->onDelete('cascade');
            $table->decimal('total_marks', 8, 2)->default(0); // Champ pour stocker les notes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_competency_type');
    }
}; 