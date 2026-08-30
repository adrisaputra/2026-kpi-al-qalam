<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'kpi_category_id',
        'name',
    ];

    public function kpi_category(){
        return $this->belongsTo('App\Models\KpiCategory');
    }

    public function kpi_indicator(){
        return $this->HasOne('App\Models\KpiIndicator');
    }

    public function kpi_indicators(){
        return $this->hasMany('App\Models\KpiIndicator');
    }

}
