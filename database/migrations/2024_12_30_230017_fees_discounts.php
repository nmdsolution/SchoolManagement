<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fees_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->json('applicable_status');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_fees_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fees_discount_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_fees_discounts');
        Schema::dropIfExists('fees_discounts');
    }
};