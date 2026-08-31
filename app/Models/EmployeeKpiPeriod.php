<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeKpiPeriod extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'employee_id',
        'month',
        'year'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee');
    }

    public function employee_kpi_indicator(){
        return $this->HasOne('App\Models\EmployeeKpiIndicator');
    }

}
