<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('centers', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('domain', 1024);
            $table->string('support_contact', 16);
            $table->string('support_email', 128);
            $table->string('logo', 512);
            $table->string('tagline', 512);
            $table->string('address', 512);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('centers');
    }
};
