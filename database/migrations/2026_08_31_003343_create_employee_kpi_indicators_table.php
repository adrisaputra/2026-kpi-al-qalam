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
        Schema::create('employee_kpi_indicators', function (Blueprint $table) {
            $table->increments('id',11);
            
            $table->unsignedInteger('employee_kpi_period_id');
            $table->foreign("employee_kpi_period_id")->references('id')->on("employee_kpi_periods");
            
            $table->unsignedInteger('kpi_indicator_id');
            $table->foreign("kpi_indicator_id")->references('id')->on("kpi_indicators");
            
            $table->boolean('value')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_kpi_indicators');
    }
};
