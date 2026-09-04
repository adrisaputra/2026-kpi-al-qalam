<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeReportCategory;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class EmployeeReportCategoryController extends Controller
{
    ## Show Data
    public function index($employee)
    {
        $title = "Rapor";
        $employee = Crypt::decrypt($employee);
        $employee = Employee::where('id',$employee)->first();
        $report_category = ReportCategory::get();
        return view('admin.employee_report_category.index', compact('title', 'employee','report_category'));
    }

    ## Get Data
     public function get_employee_report_category_index(Request $request, $employee)
    {
        if ($request->ajax()) {
            $counters = 1;

            $employee_report_category = EmployeeReportCategory::where('employee_id', $employee)->get();

            return DataTables::of($employee_report_category)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('display_report_category_name', function ($v) {
                return $v->report_category?->name;
            })
            ->addColumn('action', function ($v){
                $employee_report_period = url('employee_report_period', Crypt::encrypt($v->id));
                $btn = '<a href="'.$employee_report_period.'" title="Detail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list text-info"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </a>';
                if(Auth::user()->group->name == 'Admin KPI'){
                    $btn .= '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                            </a>';
                    $btn .= '<a href="#" onclick="deleteData('.$v->id.')" id="'.$v->id.'" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }

    
    public function validate(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'report_category_id' => 'Kategori Rapor',
            ];

            if ($action === "Simpan") {
                $rules = [
                    'report_category_id' => 'required'
                ];
            } else {
                $rules = [
                    'report_category_id' => 'required'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save KPI 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $employee_report_category = new EmployeeReportCategory();
            $employee_report_category->employee_id = $request->employee_id;
            $employee_report_category->report_category_id = $request->report_category_id;
            $employee_report_category->save();
            activity()->log('Create Employee KPI Data');
            return response()->json(['success' => true, 'message' => 'Tambah Kategori Rapor Berhasil']);
        }
    }

    ## Get KPI
    public function edit(Request $request, EmployeeReportCategory $employee_report_category)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $employee_report_category]);
        }
    }

    ## Edit KPI
    public function update(Request $request, EmployeeReportCategory $employee_report_category)
    {
        if ($request->ajax()) {

            $employee_report_category->employee_id = $request->employee_id;
            $employee_report_category->report_category_id = $request->report_category_id;
            $employee_report_category->save();

            activity()->log('Edit Employee KPI Data With ID = ' . $employee_report_category->id);
            return response()->json(['success' => true, 'message' => 'Ubah Kategori Rapor Berhasil']);
        }
    }

    ## Delete KPI
    public function delete(Request $request,  EmployeeReportCategory $employee_report_category)
    {
        if ($request->ajax()) {
            $employee_report_category->delete();
            activity()->log('Delete Employee KPI Data With ID = ' . $employee_report_category->id);
            return response()->json(['success' => true, 'message' => 'Hapus Kategori Rapor Berhasil']);
        }
    }
}
