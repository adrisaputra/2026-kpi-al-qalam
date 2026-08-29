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
}
