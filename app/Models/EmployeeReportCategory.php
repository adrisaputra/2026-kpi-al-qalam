<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReportCategory extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'employee_id',
        'report_category_id',
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function report_category(){
        return $this->belongsTo('App\Models\ReportCategory');
    }
}
