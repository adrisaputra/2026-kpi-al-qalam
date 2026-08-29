<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KpiCategoryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
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
    
    ## Employee
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/employee/list', [EmployeeController::class, 'get_employee_index'])->name('employee.list');
    Route::post('/employee/store', [EmployeeController::class, 'store']);
    Route::post('/employee/validate/{action}', [EmployeeController::class, 'validate']);
    Route::put('/employee/edit/{employee}', [EmployeeController::class, 'update']);
    Route::get('/employee/delete/{employee}',[EmployeeController::class, 'delete']);
    
    ## Kpi Category
    Route::get('/kpi_category', [KpiCategoryController::class, 'index'])->name('kpi_category.index');
    Route::get('/kpi_category/list', [KpiCategoryController::class, 'get_kpi_category_index'])->name('kpi_category.list');
    Route::post('/kpi_category/store', [KpiCategoryController::class, 'store']);
    Route::post('/kpi_category/validate/{action}', [KpiCategoryController::class, 'validate']);
    Route::get('/kpi_category/edit/{kpi_category}', [KpiCategoryController::class, 'edit']);
    Route::put('/kpi_category/edit/{kpi_category}', [KpiCategoryController::class, 'update']);
    Route::get('/kpi_category/delete/{kpi_category}',[KpiCategoryController::class, 'delete']);
    
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
