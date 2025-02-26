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
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained();
            $table->foreignId('grade_id')->nullable()->constrained();
            $table->foreignId('student_id')->constrained();
            $table->integer('oral')->nullable()->default(0);
            $table->integer('written')->nullable()->default(0);
            $table->integer('attitude')->nullable()->default(0);
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
       Schema::dropIfExists('evaluation_results');
    }
};
