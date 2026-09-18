<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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
                'category' => $v->category,
                'name' => $v->name,
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
                return $v->name;
            })
            ->addColumn('value', function ($v) {

                $value = null;

                if (in_array(Auth::user()->group->name, ['Admin KPI', 'Admin Unit'])) {

                    if (in_array($v->report->category, ['1', '2', '3', '4'])) {
                        $value = $v->value;
                    } else {
                        $url = url('employee_report_file', Crypt::encrypt($v->id));
                        $value = '<a href="' . $url . '"  class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                                    <span>Lihat File Gambar</span>';

                            if ($v->employee_report_files->count() > 0) {
                                $value .= '<span class="badge badge-danger counter">'.$v->employee_report_files->count().'</span>';

                            }

                            $value .= '</a>';
                    }

                } else {

                    if ($v->report->category == 1) {

                        $value = '
                            <div class="d-flex align-items-center" style="gap: 15px;">

                                <div class="n-chk">
                                    <label class="new-control new-radio radio-success">
                                        <input type="radio"
                                            class="new-control-input"
                                            name="value_'.$v->id.'"
                                            id="value_'.$v->id.'"
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
                                            id="value_'.$v->id.'"
                                            value="0"
                                            onchange="updateValueitem(this, '.$v->id.')"
                                            '.($v->value == 0 ? 'checked' : '').'>
                                        <span class="new-control-indicator"></span>
                                        Tidak
                                    </label>
                                </div>

                            </div>';
                       
                    } else if($v->report->category == 2) {

                        $value = '
                            <div class="d-flex align-items-center" style="gap: 15px;">

                                <div class="n-chk">
                                    <label class="new-control new-radio radio-success">
                                        <input type="radio"
                                            class="new-control-input"
                                            name="value_'.$v->id.'"
                                            id="value_'.$v->id.'"
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
                                            id="value_'.$v->id.'"
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
                                            id="value_'.$v->id.'"
                                            value="0"
                                            onchange="updateValueitem(this, '.$v->id.')"
                                            '.($v->value == 0 ? 'checked' : '').'>
                                        <span class="new-control-indicator"></span>
                                        0
                                    </label>
                                </div>

                            </div>';

                    } else if($v->report->category == 3) {
                        $value = '<input type="number" name="value_'.$v->id.'" value="'.$v->value.'" onchange="updateValueitem(this, '.$v->id.')" class="form-control form-control-sm" id="value_'.$v->id.'">';
                    } else if($v->report->category == 4) {

                        $employee = Employee::get();
                        $value = '<input type="number" name="value_'.$v->id.'" value="'.$v->value.'" onchange="updateValueitem(this, '.$v->id.')" class="form-control form-control-sm" id="value_'.$v->id.'" placeholder="Masukkan Jumlah Jam" >';
                        $value .= '<br>
                                    <select class="basic form-control form-control-sm" name="employee_id_'.$v->id.'" 
                                            onchange="updateValueitem(this, '.$v->id.')" 
                                            class="form-select form-select-sm" 
                                            id="employee_id_'.$v->id.'">
                                        <option value="">- Pegawai yang digantikan -</option>';

                        foreach ($employee as $x) {
                            $value .= '<option value="'.$x->id.'" '.($v->employee_id == $x->id ? 'selected' : '').'>'
                                        .e($x->name).
                                    '</option>';
                        }

                        $value .= '</select>';
                        $value .= '<br><input type="text" name="reason_'.$v->id.'" value="'.e($v->reason).'" onchange="updateValueitem(this, '.$v->id.')" class="form-control form-control-sm" placeholder="Alasan" id="reason_'.$v->id.'">';
                    } else if($v->report->category == 5) {
                        $url = url('employee_report_file', Crypt::encrypt($v->id));
                        $value = '<a href="' . $url . '"  class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                                    <span>Lihat File Gambar</span>';

                            if ($v->employee_report_files->count() > 0) {
                                $value .= '<span class="badge badge-danger counter">'.$v->employee_report_files->count().'</span>';

                            }

                            $value .= '</a>';
                            
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
            $employee_report->employee_id = $request->employee_id;
            $employee_report->reason = $request->reason;
            $employee_report->save();

            activity()->log('Edit Employee Report With ID = ' . $employee_report->id);

            return response()->json([
                'success' => true,
                'message' => 'Simpan Nilai Berhasil'
            ]);
        }
    }

}
