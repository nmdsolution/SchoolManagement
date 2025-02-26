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
        Schema::create('competency_domains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('number')->nullable();
            $table->integer('total_marks')->nullable()->default(0);
            $table->foreignId('center_id')->constrained();
            $table->foreignId('session_year_id')->constrained();
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
        Schema::dropIfExists('competency_domains');
    }
};
