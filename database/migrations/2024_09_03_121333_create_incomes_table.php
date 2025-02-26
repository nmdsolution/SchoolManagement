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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('invoice_id')->nullable();
            $table->decimal('quantity');
            $table->decimal('amount',10,2);
            $table->decimal('total_amount',10,2);
            $table->date('date');
            $table->integer('payment_method')->nullable();
            $table->text('note')->nullable();
            $table->text('attach')->nullable();
            $table->string('purchased_by');
            $table->string('purchased_from');
            $table->foreignId('category_id')
                ->references('id')->on('income_categories')->cascadeOnDelete();
            $table->bigInteger('session_year_id');
            $table->smallInteger('medium_id')->default(0);
            $table->foreignId('center_id')->references('id')->on('centers')->cascadeOnDelete();
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
        Schema::dropIfExists('incomes');
    }
};
