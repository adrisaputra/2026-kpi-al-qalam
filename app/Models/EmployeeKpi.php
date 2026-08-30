<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeKpi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'employee_id',
        'kpi_id',
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function kpi(){
        return $this->belongsTo('App\Models\Kpi');
    }
}
