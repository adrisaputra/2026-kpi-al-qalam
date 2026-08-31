<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeKpiIndicatorItem extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'employee_kpi_indicator_id',
        'kpi_indicator_item_id',
        'value'
    ];

    public function employee_kpi_indicator(){
        return $this->belongsTo('App\Models\EmployeeKpiIndicator');
    }

    public function kpi_indicator_item(){
        return $this->belongsTo('App\Models\KpiIndicatorItem');
    }

}
