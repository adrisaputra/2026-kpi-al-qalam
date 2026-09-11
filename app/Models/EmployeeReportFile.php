<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeReportFile extends Model
{
    protected $connection = 'mysql';
    protected $fillable = [
        'employee_report_id',
        'image',
        'desc'
    ];

    public function employee_report(){
        return $this->belongsTo('App\Models\EmployeeReport');
    }
    
}
