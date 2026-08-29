<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiIndicator extends Model
{
    use HasFactory;
    protected $fillable = [
        'kpi_id',
        'name',
        'indicator',
        'target',
    ];

    public function kpi(){
        return $this->belongsTo('App\Models\Kpi');
    }

    public function kpi_indicator_item(){
        return $this->HasOne('App\Models\KpiIndicatorItem');
    }

    public function kpi_indicator_items(){
        return $this->hasMany('App\Models\KpiIndicatorItem');
    }

}
