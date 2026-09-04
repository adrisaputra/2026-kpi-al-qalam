<?php

namespace App\Http\Controllers;

use App\Models\EmployeeKpiIndicator;
use App\Models\EmployeeKpiIndicatorItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class EmployeeKpiIndicatorItemController extends Controller
{
    public function index($employee_kpi_indicator)
    {
        $title = "KPI";
        $employee_kpi_indicator = Crypt::decrypt($employee_kpi_indicator);
        $employee_kpi_indicator = EmployeeKpiIndicator::where('id',$employee_kpi_indicator)->first();
        return view('admin.employee_kpi_indicator_item.index', compact('title', 'employee_kpi_indicator'));
    }

    
    public function get_employee_kpi_indicator_item_index(Request $request, $employee_kpi_indicator)
    {
        if ($request->ajax()) {
            $counters = 1;

            $employee_kpi_indicator = Crypt::decrypt($employee_kpi_indicator);
            $employee_kpi_indicator = EmployeeKpiIndicator::where('id', $employee_kpi_indicator)->first();

            $employee_kpi_indicator_item = EmployeeKpiIndicatorItem::where('employee_kpi_indicator_id', $employee_kpi_indicator->id)->get();

            return DataTables::of($employee_kpi_indicator_item)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('kpi_indicator_item_id', function ($v) {
                return $v->kpi_indicator_item->id;
            })
            ->addColumn('measurement_tool', function ($v) {
                return $v->kpi_indicator_item->measurement_tool;
            })
            ->addColumn('physical_evidence', function ($v) {
                return $v->kpi_indicator_item->physical_evidence;
            })
            ->addColumn('value', function ($v) use ($employee_kpi_indicator) {

                $value = null;

                $isAdmin = in_array(Auth::user()->group->name, ['Admin KPI', 'Admin Unit']);

                // Admin boleh edit jika is_employee = false
                // User/pegawai boleh edit jika is_employee = true
                $canEdit = $isAdmin
                    ? $employee_kpi_indicator->kpi_indicator->is_employee == false
                    : $employee_kpi_indicator->kpi_indicator->is_employee == true;

                if ($canEdit) {

                    $value = '
                        <div class="d-flex align-items-center" style="gap: 10px;">

                        <div class="n-chk">
                            <label class="new-control new-radio radio-success">
                            <input type="radio" class="new-control-input"name="value_'.$v->id.'"
                                                            value="1"
                                                            onchange="updateValueitem(this, '.$v->id.')"
                                                            '.($v->value == 1 ? 'checked' : '').'>
                            <span class="new-control-indicator"></span>Ada
                            </label>
                        </div>
                        <div class="n-chk">
                            <label class="new-control new-radio radio-success">
                            <input type="radio" class="new-control-input"name="value_'.$v->id.'"
                                                            value="0"
                                                            onchange="updateValueitem(this, '.$v->id.')"
                                                            '.($v->value == 0 ? 'checked' : '').'>
                            <span class="new-control-indicator"></span>Tidak Ada
                            </label>
                        </div>
                        </div>';

                } else {

                    $value = ($v->value == 1 ? 'Ada' : 'Tidak Ada');
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
    public function update(Request $request, EmployeeKpiIndicatorItem $employee_kpi_indicator_item)
    {
        if ($request->ajax()) {

            $employee_kpi_indicator_item->value = $request->value;
            $employee_kpi_indicator_item->save();

            // Hitung ulang
            $items = EmployeeKpiIndicatorItem::where(
                'employee_kpi_indicator_id',
                $employee_kpi_indicator_item->employee_kpi_indicator_id
            )->get();

            $total = $items->sum('value');
            $count = $items->count();

            $presentase = $count > 0
                ? ($total / $count) * 100
                : 0;

            // Konversi ke score 1-5
            if ($presentase >= 90) {
                $score = 5;
            } elseif ($presentase >= 80) {
                $score = 4;
            } elseif ($presentase >= 70) {
                $score = 3;
            } elseif ($presentase >= 60) {
                $score = 2;
            } else {
                $score = 1;
            }

            activity()->log('Edit Employee Kpi Indicator Item Data With ID = ' . $employee_kpi_indicator_item->id);

            return response()->json([
                'success' => true,
                'message' => 'Simpan Nilai Berhasil',
                'presentase' => $presentase,
                'score' => $score
            ]);
        }
    }

}
