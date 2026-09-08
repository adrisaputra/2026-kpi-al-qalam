<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Employee;
use App\Models\EmployeeKpi;
use App\Models\EmployeeKpiIndicator;
use App\Models\KpiCategory;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class EmployeeKpiController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Pegawai Aktif";
        $work_unit = WorkUnit::get();
        $kpi_category = KpiCategory::get();
        return view('admin.employee_kpi.index', compact('title', 'work_unit','kpi_category'));
    }

    ## Get Data
    public function get_employee_kpi_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $month = $request->input('get_month') ?? date('m');
            $year = $request->input('get_year') ?? date('Y');

            $query = Employee::query()
                ->leftJoin(
                    'work_units',
                    'work_units.id',
                    '=',
                    'employees.work_unit_id'
                )
                ->select(
                    'employees.*',
                    'work_units.name as work_unit_name'
                );
        
            if(Auth::user()->group->name == 'Admin KPI'){
                if ($request->has('get_work_unit') && !empty($request->input('get_work_unit'))) {
                    $get_work_unit = $request->input('get_work_unit');
                    $query->whereHas('work_unit', function($q) use ($get_work_unit) {
                        $q->where('id', $get_work_unit);
                    });
                }
            } else {
                $get_work_unit = Auth::user()->work_unit_id;
                $query->whereHas('work_unit', function($q) use ($get_work_unit) {
                    $q->where('id', $get_work_unit);
                });
            }

            $employee = $query->limit(10);
            
            return DataTables::of($employee)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counter) {
                    return $counter++;
                })
                ->addColumn('name_display', function ($v) {
                    return 'NIK : ' . $v->nik . '<br><b>' . $v->name . '</b>';
                })
                ->addColumn('display_work_unit_name', function ($v) {
                    return $v->work_unit->name;
                })
                ->addColumn('tmt_display', function ($v) {
                    return Helpers::date($v->tmt);
                })
                ->addColumn('education', function ($v) {
                })
                ->addColumn('score', function ($v) use ($month,$year){
                    $score = EmployeeKpiIndicator::whereHas('employee_kpi_period',
                                                function ($query) use ($v, $month, $year) {
                                                    $query->where('employee_id', $v->id)
                                                        ->where('month', $month)
                                                        ->where('year', $year);
                                                }
                                            )->sum('score');
                    return $score;
                })
                ->addColumn('value', function ($v) use ($month,$year){
                    $value = EmployeeKpiIndicator::whereHas('employee_kpi_period',
                                                function ($query) use ($v, $month, $year) {
                                                    $query->where('employee_id', $v->id)
                                                        ->where('month', $month)
                                                        ->where('year', $year);
                                                }
                                            )->sum('value');
                    return number_format(round($value, 2), 2, '.', '');
                })
                ->addColumn('action', function ($v) use ($request){
                    $kpi = url('employee_kpi_detail', Crypt::encrypt($v->id));
                    $btn = '<a href="'.$kpi.'" class="btn btn-sm mb-2 mr-1 btn-success" title="KPI">
                                    KPI
                                </a>';
                    return $btn;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nik', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('tmt', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('tmt', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('work_unit', function ($query, $keyword) {
                    $query->whereHas('work_unit', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['name_display', 'photo', 'action'])->make(true);
        }
    }

}
