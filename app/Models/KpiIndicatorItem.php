<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiIndicatorItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'kpi_indicator_id',
        'name',
        'indicator',
        'target',
    ];

    public function kpi_indicator(){
        return $this->belongsTo('App\Models\KpiIndicator');
    }
    
    public function employee_kpi_indicator_item(){
        return $this->hasOne('App\Models\EmployeeKpiIndicatorItem');
    }

}
