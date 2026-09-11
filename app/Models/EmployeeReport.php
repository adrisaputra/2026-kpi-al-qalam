<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReport extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'employee_report_period_id',
        'report_id',
        'value',
    ];

    public function employee_report_period(){
        return $this->belongsTo('App\Models\EmployeeReportPeriod');
    }
    
    public function report(){
        return $this->belongsTo('App\Models\Report');
    }

    public function employee_report_file(){
        return $this->hasOne('App\Models\EmployeeReportFile');
    }
    
    public function employee_report_files(){
        return $this->hasMany('App\Models\EmployeeReportFile');
    }
    
}
