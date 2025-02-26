<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competency_domains', function (Blueprint $table) {
            $table->foreignId('medium_id')->nullable()->constrained('mediums');
        });

        $medium = DB::table('mediums')->first();
        DB::table('competency_domains')->whereNull('medium_id')->update(['medium_id' => $medium->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competency_domains', function (Blueprint $table) {
            $table->dropConstrainedForeignId('medium_id');
        });
    }
};
