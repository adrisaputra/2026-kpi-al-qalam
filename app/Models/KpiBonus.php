<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiBonus extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'kpi_id',
        'indicator',
        'target',
        'weight',
        'is_employee',
    ];

}
