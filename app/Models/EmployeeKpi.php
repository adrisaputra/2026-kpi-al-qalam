<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeKpi extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'mysql';
    protected $fillable = [
        'employee_id',
        'kpi_id',
        'month',
        'year'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function kpi(){
        return $this->belongsTo('App\Models\Kpi');
    }
    
    public function employee_kpi_period(){
        return $this->HasOne('App\Models\EmployeeKpiPeriod');
    }

}
