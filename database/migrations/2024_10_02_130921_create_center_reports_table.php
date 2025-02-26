<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Center;
use App\Models\ReportCard;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('center_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Center::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ReportCard::class)->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('center_reports');
    }
};
