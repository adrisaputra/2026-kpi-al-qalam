<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Employee;
use App\Models\EmployeeReport;
use App\Models\EmployeeReportCategory;
use App\Models\EmployeeReportPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class EmployeeReportPeriodController extends Controller
{
    public function index($employee_report_category)
    {
        $title = "KPI";
        $employee_report_category = Crypt::decrypt($employee_report_category);
        $employee_report_category = EmployeeReportCategory::where('id',$employee_report_category)->first();
        $employee = Employee::where('id',$employee_report_category->employee_id)->first();
        return view('admin.employee_report_period.index', compact('title','employee_report_category','employee'));
    }

    
    public function get_employee_report_period_index(Request $request, $employee_report_category)
    {
        if ($request->ajax()) {
            $counters = 1;

            $month = $request->input('get_month') ? $request->input('get_month') : date('m');
            $year = $request->input('get_year') ? $request->input('get_year') : date('Y');

            $employee_report_period = EmployeeReportPeriod::where('employee_report_category_id', $employee_report_category)
                                    ->whereMonth('date', $month)->whereyear('date', $year)->get();

            return DataTables::of($employee_report_period)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('display_date', function ($v) {
                return $v->day.' / '.date('d-m-Y', strtotime($v->date));
            })
            ->addColumn('total', function ($v) {
                return $v->employee_reports->sum('value');
            })
            ->addColumn('action', function ($v) {
                $employee_report = url('employee_report_value', Crypt::encrypt($v->id));
                
                    $btn = '<a href="'.$employee_report.'" title="Detail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list text-info"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    </a>';
                
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }

    
    ## Save Employee KPI Indicator 
    public function store(Request $request)
    {
        if ($request->ajax()) {

                $month = $request->month;
                $year  = $request->year;

                $employee_report_category = EmployeeReportCategory::where('id', $request->employee_report_category_id)->first();
                
                $startDate = Carbon::create($year, $month, 1);
                $endDate   = $startDate->copy()->endOfMonth();

                while ($startDate->lte($endDate)) {

                    // Senin - Jumat saja
                    if ($startDate->isWeekday()) {

                        EmployeeReportPeriod::firstOrCreate([
                            'employee_report_category_id' => $request->employee_report_category_id,
                            'employee_id' => $employee_report_category->employee_id,
                            'day' => Helpers::day_name($startDate->format('l')),
                            'date' => $startDate->format('Y-m-d'),
                        ]);
                    }

                    $startDate->addDay();
                }


                activity()->log('Create Employee KPI Indicator Data With Employee Id = '.$request->employee_id);
                return response()->json(['success' => true, 'message' => 'Tambah Data Rapor Berhasil']);
                
        }
    }

}
