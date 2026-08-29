<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kpi_indicator_items', function (Blueprint $table) {
            $table->increments('id',11);
            
            $table->unsignedInteger('kpi_indicator_id');
            $table->foreign("kpi_indicator_id")->references('id')->on("kpi_indicators");
            
            $table->string('measurement_tool')->nullable();
            $table->string('physical_evidence')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_indicator_items');
    }
};
