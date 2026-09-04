<?php

namespace App\Http\Controllers;

use App\Models\EmployeeReport;
use App\Models\EmployeeReportPeriod;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class EmployeeReportValueController extends Controller
{
    public function index($employee_report_period)
    {
        $title = "KPI";
        $employee_report_period = Crypt::decrypt($employee_report_period);
        $employee_report_period = EmployeeReportPeriod::where('id',$employee_report_period)->first();
        $report = Report::where('report_category_id',$employee_report_period->employee_report_category->report_category_id)->get();
        foreach($report as $v){
            EmployeeReport::firstOrCreate([
                'employee_report_period_id' => $employee_report_period->id,
                'report_id' => $v->id,
            ]);
        }
        return view('admin.employee_report_value.index', compact('title', 'employee_report_period'));
    }

    
    public function get_employee_report_value_index(Request $request, $employee_report_period)
    {
        if ($request->ajax()) {
            $counters = 1;

            $employee_report_period = Crypt::decrypt($employee_report_period);
            $employee_report_period = EmployeeReportPeriod::where('id', $employee_report_period)->first();

            $employee_report = EmployeeReport::where('employee_report_period_id', $employee_report_period->id)->get();

            return DataTables::of($employee_report)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('name', function ($v) {
                return $v->report->name;
            })
            ->addColumn('value', function ($v) {

                $value = null;

                if (in_array(Auth::user()->group->name, ['Admin KPI', 'Admin Unit'])) {

                    if ($v->report->is_special_value == true) {
                        $value = in_array($v->value, [1, 2]) ? 'Ada' : 'Tidak Ada';
                    } else {
                        $value = ($v->value == 1 ? 'Ada' : 'Tidak Ada');
                    }

                } else {

                    if ($v->report->is_special_value == true) {

                        $value = '
                            <div class="d-flex align-items-center" style="gap: 15px;">

                                <div class="n-chk">
                                    <label class="new-control new-radio radio-success">
                                        <input type="radio"
                                            class="new-control-input"
                                            name="value_'.$v->id.'"
                                            value="1"
                                            onchange="updateValueitem(this, '.$v->id.')"
                                            '.($v->value == 1 ? 'checked' : '').'>
                                        <span class="new-control-indicator"></span>
                                        1
                                    </label>
                                </div>

                                <div class="n-chk">
                                    <label class="new-control new-radio radio-success">
                                        <input type="radio"
                                            class="new-control-input"
                                            name="value_'.$v->id.'"
                                            value="2"
                                            onchange="updateValueitem(this, '.$v->id.')"
                                            '.($v->value == 2 ? 'checked' : '').'>
                                        <span class="new-control-indicator"></span>
                                        2
                                    </label>
                                </div>

                                <div class="n-chk">
                                    <label class="new-control new-radio radio-success">
                                        <input type="radio"
                                            class="new-control-input"
                                            name="value_'.$v->id.'"
                                            value="0"
                                            onchange="updateValueitem(this, '.$v->id.')"
                                            '.($v->value == 0 ? 'checked' : '').'>
                                        <span class="new-control-indicator"></span>
                                        0
                                    </label>
                                </div>

                            </div>';

                    } else {

                        $value = '
                            <div class="d-flex align-items-center" style="gap: 15px;">

                                <div class="n-chk">
                                    <label class="new-control new-radio radio-success">
                                        <input type="radio"
                                            class="new-control-input"
                                            name="value_'.$v->id.'"
                                            value="1"
                                            onchange="updateValueitem(this, '.$v->id.')"
                                            '.($v->value == 1 ? 'checked' : '').'>
                                        <span class="new-control-indicator"></span>
                                        Ya
                                    </label>
                                </div>

                                <div class="n-chk">
                                    <label class="new-control new-radio radio-success">
                                        <input type="radio"
                                            class="new-control-input"
                                            name="value_'.$v->id.'"
                                            value="0"
                                            onchange="updateValueitem(this, '.$v->id.')"
                                            '.($v->value == 0 ? 'checked' : '').'>
                                        <span class="new-control-indicator"></span>
                                        Tidak
                                    </label>
                                </div>

                            </div>';
                    }
                }

                return $value;
            })
            ->addColumn('value_raw', function ($v) {
                return $v->value;
            })
            ->rawColumns(['value'])
            ->make(true);
        }
    }

    ## Update
    public function update(Request $request, EmployeeReport $employee_report)
    {
        if ($request->ajax()) {

            $employee_report->value = $request->value;
            $employee_report->save();

            activity()->log('Edit Employee Report With ID = ' . $employee_report->id);

            return response()->json([
                'success' => true,
                'message' => 'Simpan Nilai Berhasil'
            ]);
        }
    }

}
