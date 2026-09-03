<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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
        } if(Auth::user()->group_id == 5){
            $employee = 2;
            $employee_l = 12;
            $employee_p = 123;
            return view('admin.home', compact('title','employee','employee_l','employee_p'));
        } elseif(Auth::user()->group_id == 3){
            return view('admin.home', compact('title'));
        } 
    }
}