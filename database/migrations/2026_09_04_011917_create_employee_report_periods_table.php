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
        Schema::create('employee_report_periods', function (Blueprint $table) {
            $table->increments('id',11);
            
            $table->unsignedInteger('employee_report_category_id');
            $table->foreign("employee_report_category_id")->references('id')->on("employee_report_categories");
            
            $table->unsignedInteger('employee_id');
            
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_report_periods');
    }
};
