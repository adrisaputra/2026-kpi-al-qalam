<?php

namespace App\Http\Controllers;

use App\Models\EmployeeKpiBonus;
use Illuminate\Http\Request;

class EmployeeKpiBonusController extends Controller
{
    
    public function validate(Request $request)
    {
        if ($request->ajax()) {

            $attributes = [
                'score' => 'Skor',
                'value' => 'Nilai'
            ];

            $rules = [
                'score' => 'required|max:255',
                'value' => 'required|max:255'
            ];

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Get Employee KPI Bonus
    public function edit(Request $request, EmployeeKpiBonus $employee_kpi_bonus)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $employee_kpi_bonus]);
        }
    }

    ## Edit Employee KPI Bonus
    public function update(Request $request, EmployeeKpiBonus $employee_kpi_bonus)
    {
        if ($request->ajax()) {

            $employee_kpi_bonus->score = $request->score;
            $employee_kpi_bonus->value = $request->value;
            $employee_kpi_bonus->save();

            activity()->log('Edit Employee KPI Bonus Data With ID = ' . $employee_kpi_bonus->id);
            return response()->json(['success' => true, 'message' => 'Ubah Indikator KPI Berhasil']);
        }
    }
}
