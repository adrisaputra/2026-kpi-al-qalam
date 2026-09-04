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
        Schema::create('employee_reports', function (Blueprint $table) {
            $table->increments('id',11);
            
            $table->unsignedInteger('employee_report_period_id');
            $table->foreign("employee_report_period_id")->references('id')->on("employee_report_periods");
            
            $table->unsignedInteger('report_id');
            $table->foreign("report_id")->references('id')->on("reports");
            
            $table->unsignedTinyInteger('value')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_reports');
    }
};
