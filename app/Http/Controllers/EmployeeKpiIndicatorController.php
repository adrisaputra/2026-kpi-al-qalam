<?php

namespace App\Http\Controllers;

use App\Models\EmployeeKpi;
use App\Models\EmployeeKpiIndicator;
use App\Models\EmployeeKpiPeriod;
use App\Models\KpiIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeKpiIndicatorController extends Controller
{
    
    ## Save Employee KPI Indicator 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            // DB::transaction(function () use ($request) {
                
                $employee_kpi = EmployeeKpi::with('kpi')->where('employee_id', $request->employee_id)->first();
                $employee_kpi_period = EmployeeKpiPeriod::firstOrCreate(
                                            [
                                                'employee_id' => $request->employee_id,
                                                'month' => $request->month,
                                                'year' => $request->year,
                                            ]
                                        );

                // Cek apakah indikator KPI untuk periode ini sudah pernah dibuat
                $check_employee_kpi_indicator = EmployeeKpiIndicator::where(
                    'employee_kpi_period_id',
                    $employee_kpi_period->id
                )->exists();

                if ($check_employee_kpi_indicator) {
                    return response()->json(['success' => false,'message' => 'Data indikator KPI untuk periode ini sudah ada.']);
                }

                $kpi_indicator = KpiIndicator::where('kpi_id',$employee_kpi->kpi->id)->get();
                
                foreach($kpi_indicator as $v){
                    $employee_kpi_indicator = new EmployeeKpiIndicator();
                    $employee_kpi_indicator->employee_kpi_period_id = $employee_kpi_period->id;
                    $employee_kpi_indicator->kpi_indicator_id = $v->id;
                    $employee_kpi_indicator->save();
                }

                activity()->log('Create Employee KPI Indicator Data With Employee Id = '.$request->employee_id);
                return response()->json(['success' => true, 'message' => 'Tambah Indikator KPI Berhasil']);
                
            // });
        }
    }

}
