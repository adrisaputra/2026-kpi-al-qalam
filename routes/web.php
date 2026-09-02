<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeKpiController;
use App\Http\Controllers\EmployeeKpiIndicatorController;
use App\Http\Controllers\EmployeeKpiIndicatorItemController;
use App\Http\Controllers\EmployeeKpiPeriodController;
use App\Http\Controllers\EmployeeReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KpiCategoryController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\KpiIndicatorController;
use App\Http\Controllers\KpiIndicatorItemController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReportCategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/buat_storage', function () {
    Artisan::call('storage:link');
    dd("Storage Berhasil Di Buat");
});

Route::get('/clear-cache-all', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    dd("Cache Clear All");
});


Route::get('/', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);


Route::middleware(['role:Admin KPI,Employee'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index']);
});

Route::middleware(['role:Admin KPI'])->group(function () {
    
    ## Employee KPI
    Route::get('/employee_kpi', [EmployeeKpiController::class, 'index'])->name('employee_kpi.index');
    Route::get('/employee_kpi/list', [EmployeeKpiController::class, 'get_employee_kpi_index'])->name('employee_kpi.list');
    Route::post('/employee_kpi/store', [EmployeeKpiController::class, 'store']);
    Route::post('/employee_kpi/validate', [EmployeeKpiController::class, 'validate']);
    Route::get('/employee_kpi/edit/{employee}', [EmployeeKpiController::class, 'edit']);
    Route::put('/employee_kpi/edit/{employee}', [EmployeeKpiController::class, 'update']);
    
    ## Employee Raport
    Route::get('/employee_report', [EmployeeReportController::class, 'index'])->name('employee_report.index');
    Route::get('/employee_report/list', [EmployeeReportController::class, 'get_employee_index'])->name('employee_report.list');
    Route::post('/employee_report/store', [EmployeeReportController::class, 'store']);
    Route::post('/employee_report/validate', [EmployeeReportController::class, 'validate']);
    Route::get('/employee_report/edit/{employee}', [EmployeeReportController::class, 'edit']);
    Route::put('/employee_report/edit/{employee}', [EmployeeReportController::class, 'update']);
    
    ## Employee KPI Periode
    Route::get('/employee_kpi_period/{employee}', [EmployeeKpiPeriodController::class, 'index'])->name('employee_kpi_period.index');
    Route::get('/employee_kpi_period/list/{employee}', [EmployeeKpiPeriodController::class, 'get_employee_kpi_period_index'])->name('employee_kpi_period.list');
    
    ## Employee KPI Indicator
    Route::post('/employee_kpi_indicator/store', [EmployeeKpiIndicatorController::class, 'store']);

    ## Employee KPI Indicator Item
    Route::get('/employee_kpi_indicator_item/{employee_kpi_indicator}', [EmployeeKpiIndicatorItemController::class, 'index'])->name('employee_kpi_indicator_item.index');
    Route::get('/employee_kpi_indicator_item/list/{employee_kpi_indicator}', [EmployeeKpiIndicatorItemController::class, 'get_employee_kpi_indicator_item_index'])->name('employee_kpi_indicator_item.list');
    Route::put('/employee_kpi_indicator_item/edit/{employee_kpi_indicator_item}', [EmployeeKpiIndicatorItemController::class, 'update']);
    
    ## KPI Category
    Route::get('/kpi_category', [KpiCategoryController::class, 'index'])->name('kpi_category.index');
    Route::get('/kpi_category/list', [KpiCategoryController::class, 'get_kpi_category_index'])->name('kpi_category.list');
    Route::post('/kpi_category/store', [KpiCategoryController::class, 'store']);
    Route::post('/kpi_category/validate/{action}', [KpiCategoryController::class, 'validate']);
    Route::get('/kpi_category/edit/{kpi_category}', [KpiCategoryController::class, 'edit']);
    Route::put('/kpi_category/edit/{kpi_category}', [KpiCategoryController::class, 'update']);
    Route::get('/kpi_category/delete/{kpi_category}',[KpiCategoryController::class, 'delete']);

    ## KPI
    Route::get('/kpi/{kpi_category}', [KpiController::class, 'index'])->name('kpi.index');
    Route::get('/kpi/list/{kpi_category}', [KpiController::class, 'get_kpi_index'])->name('kpi.list');
    Route::post('/kpi/store', [KpiController::class, 'store']);
    Route::post('/kpi/validate/{action}', [KpiController::class, 'validate']);
    Route::get('/kpi/edit/{kpi}', [KpiController::class, 'edit']);
    Route::put('/kpi/edit/{kpi}', [KpiController::class, 'update']);
    Route::get('/kpi/delete/{kpi}',[KpiController::class, 'delete']);
    Route::get('/kpi/get/{kpi_category}/{kpi?}',[KpiController::class, 'get']);

    ## KPI Indicator
    Route::get('/kpi_indicator/{kpi}', [KpiIndicatorController::class, 'index'])->name('kpi_indicator.index');
    Route::get('/kpi_indicator/list/{kpi}', [KpiIndicatorController::class, 'get_kpi_indicator_index'])->name('kpi_indicator.list');
    Route::post('/kpi_indicator/store', [KpiIndicatorController::class, 'store']);
    Route::post('/kpi_indicator/validate/{action}', [KpiIndicatorController::class, 'validate']);
    Route::get('/kpi_indicator/edit/{kpi_indicator}', [KpiIndicatorController::class, 'edit']);
    Route::put('/kpi_indicator/edit/{kpi_indicator}', [KpiIndicatorController::class, 'update']);
    Route::get('/kpi_indicator/delete/{kpi_indicator}',[KpiIndicatorController::class, 'delete']);

    ## KPI Indicator Item
    Route::get('/kpi_indicator_item/{kpi_indicator}', [KpiIndicatorItemController::class, 'index'])->name('kpi_indicator_item.index');
    Route::get('/kpi_indicator_item/list/{kpi_indicator}', [KpiIndicatorItemController::class, 'get_kpi_indicator_item_index'])->name('kpi_indicator_item.list');
    Route::post('/kpi_indicator_item/store', [KpiIndicatorItemController::class, 'store']);
    Route::post('/kpi_indicator_item/validate/{action}', [KpiIndicatorItemController::class, 'validate']);
    Route::get('/kpi_indicator_item/edit/{kpi_indicator_item}', [KpiIndicatorItemController::class, 'edit']);
    Route::put('/kpi_indicator_item/edit/{kpi_indicator_item}', [KpiIndicatorItemController::class, 'update']);
    Route::get('/kpi_indicator_item/delete/{kpi_indicator_item}',[KpiIndicatorItemController::class, 'delete']);

    ## Report Category
    Route::get('/report_category', [ReportCategoryController::class, 'index'])->name('report_category.index');
    Route::get('/report_category/list', [ReportCategoryController::class, 'get_report_category_index'])->name('report_category.list');
    Route::post('/report_category/store', [ReportCategoryController::class, 'store']);
    Route::post('/report_category/validate/{action}', [ReportCategoryController::class, 'validate']);
    Route::get('/report_category/edit/{report_category}', [ReportCategoryController::class, 'edit']);
    Route::put('/report_category/edit/{report_category}', [ReportCategoryController::class, 'update']);
    Route::get('/report_category/delete/{report_category}',[ReportCategoryController::class, 'delete']);

    ## Report
    Route::get('/report/{report_category}', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/list/{report_category}', [ReportController::class, 'get_report_index'])->name('report.list');
    Route::post('/report/store', [ReportController::class, 'store']);
    Route::post('/report/validate/{action}', [ReportController::class, 'validate']);
    Route::get('/report/edit/{report}', [ReportController::class, 'edit']);
    Route::put('/report/edit/{report}', [ReportController::class, 'update']);
    Route::get('/report/delete/{report}',[ReportController::class, 'delete']);
    Route::get('/report/get/{report_category}/{report?}',[ReportController::class, 'get']);

    ## User
    Route::get('/user', [UserController::class, 'index'])->name('users.index');
    Route::get('/user/list', [UserController::class, 'get_user_index'])->name('users.list');
    Route::post('/user/store', [UserController::class, 'store']);
    Route::post('/user/validate/{action}', [UserController::class, 'validate']);
    Route::get('/user/edit/{user}', [UserController::class, 'edit']);
    Route::put('/user/edit/{user}', [UserController::class, 'update']);
    Route::get('/user/delete/{user}',[UserController::class, 'delete']);

    ## Log
    Route::get('/log', [LogController::class, 'index'])->name('logs.index');
    Route::get('/log/list', [LogController::class, 'get_log_index'])->name('logs.list');
    Route::get('/log/detail/{user}', [LogController::class, 'detail']);

    ## Setting
    Route::get('/setting', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/setting/validate', [SettingController::class, 'validate']);
    Route::put('/setting/edit/{setting}', [SettingController::class, 'update']);
   
});
