<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeKpi;
use App\Models\KpiCategory;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class EmployeeKpiController extends Controller
{
    ## Show Data
    public function index(Request $request)
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

            $kpi = config('database.connections.mysql.database');

            $query = Employee::query()
                ->leftJoin(
                    'work_units',
                    'work_units.id',
                    '=',
                    'employees.work_unit_id'
                )
                ->leftJoin(
                    $kpi . '.employee_kpis',
                    $kpi . '.employee_kpis.employee_id',
                    '=',
                    'employees.id'
                )
                ->leftJoin(
                    $kpi . '.kpis',
                    $kpi . '.kpis.id',
                    '=',
                    $kpi . '.employee_kpis.kpi_id'
                )
                ->leftJoin(
                    $kpi . '.kpi_categories',
                    $kpi . '.kpi_categories.id',
                    '=',
                    $kpi . '.kpis.kpi_category_id'
                )
                ->select(
                    'employees.*',
                    'work_units.name as work_unit_name',
                    $kpi . '.kpi_categories.name as kpi_category_name',
                    $kpi . '.kpis.name as kpi_name'
                );
        
            if ($request->has('get_work_unit') && !empty($request->input('get_work_unit'))) {
                $get_work_unit = $request->input('get_work_unit');
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
                ->addColumn('display_kpi_category_name', function ($v) {
                    return $v->employee_kpi?->kpi?->kpi_category?->name;
                })
                ->addColumn('display_kpi_name', function ($v) {
                    return $v->employee_kpi?->kpi?->name;
                })
                ->addColumn('action', function ($v) use ($request){
                    $kpi = url('employee_kpi_period', Crypt::encrypt($v->id));
                    $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal" class="btn btn-sm mb-2 mr-1 btn-warning" title="KPI">
                                Edit
                            </a>';
                    if($v->employee_kpi){
                        $btn .= '<a href="'.$kpi.'" class="btn btn-sm mb-2 mr-1 btn-success" title="KPI">
                                    KPI
                                </a>';
                    }
                    return $btn;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('employees.name', 'like', "%{$keyword}%")
                        ->orWhere('employees.nik', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('work_unit_name', function ($query, $keyword) {
                    $query->where('work_units.name','like',"%{$keyword}%");
                })
                ->filterColumn('kpi_category_name', function ($query, $keyword) use ($kpi) {
                    $query->where($kpi . '.kpi_categories.name','like',"%{$keyword}%");
                })
                ->filterColumn('kpi_name', function ($query, $keyword) use ($kpi) {
                    $query->where($kpi . '.kpis.name','like',"%{$keyword}%");
                })
                ->rawColumns(['name_display', 'photo', 'action'])->make(true);
        }
    }

    public function validate(Request $request)
    {
        if ($request->ajax()) {

            $attributes = [
                'kpi_category_id' => 'Kategori KPI',
                'kpi_id' => 'KPI'
            ];

            $rules = [
                'kpi_category_id' => 'required',
                'kpi_id' => 'required'
            ];

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Get Data
    public function edit(Request $request, Employee $employee)
    {
        if ($request->ajax()) {
            $work_unit = WorkUnit::where('id',$employee->work_unit_id)->first();
            $employee_kpi = EmployeeKpi::with('kpi')->where('employee_id',$employee->id)->first();
            return response()->json(['success' => true, 'data' => $employee, 'work_unit' => $work_unit, 'employee_kpi' => $employee_kpi]);
        }
    }

    
    ## Edit Subdistrict
    public function update(Request $request, Employee $employee)
    {
        if ($request->ajax()) {

            $employee_kpi = EmployeeKpi::where('employee_id',$employee->id)->first();
            if($employee_kpi){
                $employee_kpi->employee_id = $employee->id;
                $employee_kpi->kpi_id = $request->kpi_id;
                $employee_kpi->save();
            } else {
                $employee_kpi = new EmployeeKpi();
                $employee_kpi->employee_id = $employee->id;
                $employee_kpi->kpi_id = $request->kpi_id;
                $employee_kpi->save();
            }

            activity()->log('Edit Employee KPI With Employee ID = ' . $employee->id);
            return response()->json(['success' => true, 'message' => 'Simpan Kategori KPI Berhasil']);
        }
    }

}
