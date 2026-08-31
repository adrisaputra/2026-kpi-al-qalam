<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeKpiIndicator;
use App\Models\EmployeeKpiPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class EmployeeKpiPeriodController extends Controller
{
    public function index($employee)
    {
        $title = "KPI";
        $employee = Crypt::decrypt($employee);
        $employee = Employee::where('id',$employee)->first();
        // $kpi_category = KpiCategory::where('id', $kpi_category)->first();
        return view('admin.employee_kpi_period.index', compact('title', 'employee'));
    }


    public function get_employee_kpi_period_index(Request $request)
    {
        if ($request->ajax()) {
            $counters = 1;

            $month = $request->input('get_month') ? $request->input('get_month') : date('m');
            $year = $request->input('get_year') ? $request->input('get_year') : date('Y');

            $employee_kpi_period = EmployeeKpiPeriod::where('month', $month)->where('year', $year)->first();
            if($employee_kpi_period ){
                $employee_kpi_indicator = EmployeeKpiIndicator::with('kpi_indicator')
                                        ->where('employee_kpi_period_id', $employee_kpi_period->id)->limit(10);
            } else {
                $employee_kpi_indicator = EmployeeKpiIndicator::where('employee_kpi_period_id', NULL)->limit(10);
            }

            return DataTables::of($employee_kpi_indicator)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('indicator_id', function ($v) {
                return $v->kpi_indicator->id;
            })
            ->addColumn('indicator', function ($v) {
                return $v->kpi_indicator->indicator;
            })
            ->addColumn('target', function ($v) {
                return $v->kpi_indicator->target;
            })
            // ->addColumn('kpi_indicator', function ($v) {
            //     $url = url('kpi_indicator', Crypt::encrypt($v->id));
            //     $btn = '<a href="' . $url . '" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
            //                 <span>Lihat Indikator KPI</span>';

            //         if ($v->kpi_indicators->count() > 0) {
            //             $btn .= '<span class="badge badge-danger counter">'.$v->kpi_indicators->count().'</span>';

            //         }

            //         $btn .= '</a>';
                    
            //     return $btn;
            // })
            ->addColumn('action', function ($v) {
                $report = url('employee_report', Crypt::encrypt($v->id));
                $btn = '<a href="#" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list text-info"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }

    // public function validate(Request $request, $action)
    // {
    //     if ($request->ajax()) {

    //         $attributes = [
    //             'name' => 'Nama KPI'
    //         ];

    //         if ($action === "Simpan") {
    //             $rules = [
    //                 'name' => 'required|max:255'
    //             ];
    //         } else {
    //             $rules = [
    //                 'name' => 'required|max:255'
    //             ];
    //         }

    //         $request->validate($rules, [], $attributes);

    //         return response()->json(['success' => true]);
    //     }
    // }

    // ## Save KPI 
    // public function store(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $kpi = new Kpi();
    //         $kpi->kpi_category_id = $request->kpi_category_id;
    //         $kpi->name = $request->name;
    //         $kpi->save();
    //         activity()->log('Create KPI Data');
    //         return response()->json(['success' => true, 'message' => 'Tambah KPI Berhasil']);
    //     }
    // }

    // ## Get KPI
    // public function edit(Request $request, Kpi $kpi)
    // {
    //     if ($request->ajax()) {
    //         return response()->json(['success' => true, 'data' => $kpi]);
    //     }
    // }

    // ## Edit KPI
    // public function update(Request $request, Kpi $kpi)
    // {
    //     if ($request->ajax()) {

    //         $kpi->kpi_category_id = $request->kpi_category_id;
    //         $kpi->name = $request->name;
    //         $kpi->save();

    //         activity()->log('Edit KPI Data With ID = ' . $kpi->id);
    //         return response()->json(['success' => true, 'message' => 'Ubah KPI Berhasil']);
    //     }
    // }

    // ## Delete KPI
    // public function delete(Request $request, Kpi $kpi)
    // {
    //     if ($request->ajax()) {
    //         $kpi->delete();
    //         activity()->log('Delete KPI Data With ID = ' . $kpi->id);
    //         return response()->json(['success' => true, 'message' => 'Hapus KPI Berhasil']);
    //     }
    // }

    // // ## Get Data
    // public function get($kpi_category, $kpi_id = NULL)
    // {
    //     $kpi = Kpi::where('kpi_category_id', $kpi_category)
    //         ->orderBy('id', 'ASC')->get();

    //     echo "<option value=''>- Pilih KPI -</option>";
    //     foreach ($kpi as $v) {
    //         if ($kpi_id) {
    //             if ($kpi_id == $v->id) {
    //                 echo "<option value='" . $v->id . "' selected>" . $v->name . "</option>";
    //             } else {
    //                 echo "<option value='" . $v->id . "' >" . $v->name . "</option>";
    //             }
    //         } else {
    //             echo "<option value='" . $v->id . "' >" . $v->name . "</option>";
    //         }
    //     }
    // }
}
