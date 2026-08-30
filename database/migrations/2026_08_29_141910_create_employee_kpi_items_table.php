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
        Schema::create('employee_kpi_items', function (Blueprint $table) {
            $table->increments('id',11);
            
            $table->unsignedInteger('employee_kpi_id');
            $table->foreign("employee_kpi_id")->references('id')->on("employee_kpis");
            
            $table->unsignedInteger('kpi_indicator_item_id');
            $table->foreign("kpi_indicator_item_id")->references('id')->on("kpi_indicator_items");
            
            $table->boolean('value')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_kpi_items');
    }
};
