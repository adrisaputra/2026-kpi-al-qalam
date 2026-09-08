<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeKpiIndicator;
use App\Models\EmployeeReport;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    ## Show Data
    public function index(Request $request)
    {
        $title = "Dashboard";
        if(Auth::user()->group->name == 'Admin KPI'){
            $employee = Employee::count();
            $employee_l = Employee::where('gender','Male')->count();
            $employee_p = Employee::where('gender','Female')->count();
            return view('admin.home', compact('title','employee','employee_l','employee_p'));
        } elseif(Auth::user()->group->name == 'Admin Unit'){
            $employee = Employee::where('work_unit_id',Auth::user()->work_unit_id)->count();
            $employee_l = Employee::where('work_unit_id',Auth::user()->work_unit_id)->where('gender','Male')->count();
            $employee_p = Employee::where('work_unit_id',Auth::user()->work_unit_id)->where('gender','Female')->count();
            return view('admin.home', compact('title','employee','employee_l','employee_p'));
        }else{
            
            $kpi = EmployeeKpiIndicator::whereHas('employee_kpi_period',
                                                function ($query){
                                                    $query->where('employee_id', Auth::user()->employee_id)
                                                        ->where('month', date('m'))
                                                        ->where('year', date('Y'));
                                                }
                                            )->sum('value');
            $report = EmployeeReport::whereHas('employee_report_period',
                                        function ($query) {
                                            $query->where('employee_id', Auth::user()->employee_id)
                                                ->whereMonth('date', date('m'))
                                                ->whereYear('date', date('Y'));
                                        }
                                    )->sum('value');
            return view('admin.home', compact('title','kpi','report'));
        } 
    }
}