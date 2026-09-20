<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Employee;
use App\Models\EmployeeKpi;
use App\Models\EmployeeKpiBonus;
use App\Models\EmployeeKpiIndicator;
use App\Models\EmployeeKpiIndicatorItem;
use App\Models\EmployeeKpiPeriod;
use App\Models\KpiBonus;
use App\Models\KpiIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class EmployeeKpiPeriodController extends Controller
{
    public function index($employee_kpi)
    {
        $title = "KPI";
        $employee_kpi = Crypt::decrypt($employee_kpi);
        $employee_kpi = EmployeeKpi::with('kpi')->where('id', $employee_kpi)->first();
        $employee = Employee::where('id',$employee_kpi->employee_id)->first();

        ## Create Employee Kpi Period
        $employee_kpi_period = EmployeeKpiPeriod::firstOrCreate(
                                    [
                                        'employee_kpi_id' => $employee_kpi->id,
                                        'employee_id' => $employee_kpi->employee_id,
                                        'month' => $employee_kpi->month,
                                        'year' => $employee_kpi->year,
                                    ]
                                );

        // Cek apakah indikator KPI untuk periode ini sudah pernah dibuat
        $check_employee_kpi_indicator = EmployeeKpiIndicator::where('employee_kpi_period_id',$employee_kpi_period->id)->first();

        if (!$check_employee_kpi_indicator) {
            
            DB::transaction(function () use ($employee_kpi, $employee_kpi_period) {
                    
                $kpi_indicator = KpiIndicator::where('kpi_id',$employee_kpi->kpi->id)->get();
                
                foreach($kpi_indicator as $v){

                    $employee_kpi_indicator = new EmployeeKpiIndicator();
                    $employee_kpi_indicator->employee_kpi_period_id = $employee_kpi_period->id;
                    $employee_kpi_indicator->kpi_indicator_id = $v->id;
                    $employee_kpi_indicator->indicator = $v->indicator;
                    $employee_kpi_indicator->target = $v->target;
                    $employee_kpi_indicator->weight = $v->weight;
                    $employee_kpi_indicator->score = 1;
                    $employee_kpi_indicator->value = ($v->weight / 5) * 1;
                    $employee_kpi_indicator->save();

                    foreach($v->kpi_indicator_items as $x){    
                        $employee_kpi_indicator_item = new EmployeeKpiIndicatorItem();
                        $employee_kpi_indicator_item->employee_kpi_indicator_id = $employee_kpi_indicator->id;
                        $employee_kpi_indicator_item->kpi_indicator_item_id = $x->id;
                        $employee_kpi_indicator_item->measurement_tool = $x->measurement_tool;
                        $employee_kpi_indicator_item->physical_evidence = $x->physical_evidence;
                        $employee_kpi_indicator_item->save();
                    }
                }

                $kpi_bonus = KpiBonus::where('kpi_id',$employee_kpi->kpi->id)->get();
                foreach($kpi_bonus as $v){
                    $employee_kpi_bonus = new EmployeeKpiBonus();
                    $employee_kpi_bonus->employee_kpi_period_id = $employee_kpi_period->id;
                    $employee_kpi_bonus->kpi_indicator_id = $v->id;
                    $employee_kpi_bonus->indicator = $v->indicator;
                    $employee_kpi_bonus->category = $v->category;
                    $employee_kpi_bonus->save();
                }
                
            });
        }

        return view('admin.employee_kpi_period.index', compact('title','employee_kpi','employee'));
    }

    public function get_employee_kpi_period_index(Request $request, $employee_kpi)
    {
        if ($request->ajax()) {
            $counters = 1;

            $month = $request->input('get_month') ? $request->input('get_month') : date('m');
            $year = $request->input('get_year') ? $request->input('get_year') : date('Y');

            $employee_kpi_period = EmployeeKpiPeriod::where('employee_kpi_id', $employee_kpi)->where('month', $month)->where('year', $year)->first();
            if($employee_kpi_period ){
                $employee_kpi_indicator = EmployeeKpiIndicator::with('kpi_indicator')
                                        ->where('employee_kpi_period_id', $employee_kpi_period->id)->get();
            } else {
                $employee_kpi_indicator = EmployeeKpiIndicator::where('employee_kpi_period_id', NULL)->get();
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
                return $v->indicator;
            })
            ->addColumn('target', function ($v) {
                return $v->target;
            })
            ->addColumn('weight', function ($v) {
                return $v->weight;
            })
            ->addColumn('score', function ($v) {
                // return $this->calculateKpiScore($v);
                return $v->score;
            })
            ->addColumn('value', function ($v) {
                // $score = $this->calculateKpiScore($v);
                // return ($v->kpi_indicator->weight / 5) * $score;
                return $v->value;
            })
            ->addColumn('action', function ($v) {
                $employee_kpi_item = url('employee_kpi_indicator_item', Crypt::encrypt($v->id));
                if(in_array(Auth::user()->group->name,['Admin KPI','Admin Unit'])){ 
                    $btn = '<a href="'.$employee_kpi_item.'" title="Detail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                    </a>';
                }else{ 
                    if($v->kpi_indicator->is_employee == true){
                        $btn = '<a href="'.$employee_kpi_item.'" title="Detail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                    } else {
                        $btn = '<a href="'.$employee_kpi_item.'" title="Detail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list text-info"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </a>';
                    }
                }
                
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }

    public function get_employee_kpi_period_bonus_index(Request $request, $employee_kpi)
    {
        if ($request->ajax()) {
            $counters = 1;

            $month = $request->input('get_month') ? $request->input('get_month') : date('m');
            $year = $request->input('get_year') ? $request->input('get_year') : date('Y');

            $employee_kpi_period = EmployeeKpiPeriod::where('employee_kpi_id', $employee_kpi)->where('month', $month)->where('year', $year)->first();
            if($employee_kpi_period ){
                $employee_kpi_bonus = EmployeeKpiBonus::with('kpi_indicator')
                                        ->where('employee_kpi_period_id', $employee_kpi_period->id)->get();
            } else {
                $employee_kpi_bonus = EmployeeKpiBonus::where('employee_kpi_period_id', NULL)->get();
            }

            return DataTables::of($employee_kpi_bonus)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('indicator_id', function ($v) {
                return $v->kpi_indicator->id;
            })
            ->addColumn('indicator', function ($v) {
                return $v->indicator;
            })
            ->addColumn('target', function ($v) {
                return $v->target;
            })
            ->addColumn('weight', function ($v) {
                return $v->weight;
            })
            ->addColumn('score', function ($v) {
                return Helpers::format_number($v->score);
            })
            ->addColumn('value', function ($v) {
                return Helpers::format_number($v->value);
            })
            ->addColumn('action', function ($v) {
                $btn = null;
                if(in_array(Auth::user()->group->name,['Admin KPI'])){ 
                    $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                }
                
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }

    function calculateKpiScore($v){
        $total = EmployeeKpiIndicatorItem::where(
            'employee_kpi_indicator_id',
            $v->id
        )->sum('value');

        $count = EmployeeKpiIndicatorItem::where(
            'employee_kpi_indicator_id',
            $v->id
        )->count();

        $score = $count > 0 ? ($total / $count) * 100 : 0;

        return match (true) {
            $score >= 90 => 5,
            $score >= 80 => 4,
            $score >= 70 => 3,
            $score >= 60 => 2,
            default      => 1,
        };
    }
}
