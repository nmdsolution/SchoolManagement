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
        Schema::create('competency_competency_type', function (Blueprint $table) {
            $table->foreignId('competency_id')->constrained('competencies')->onDelete('cascade');
            $table->foreignId('competency_type_id')->constrained('competency_types')->onDelete('cascade');
            $table->double('total_marks', 8, 2)->default(0);
            $table->primary(['competency_id', 'competency_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competency_competency_type');
    }
};
