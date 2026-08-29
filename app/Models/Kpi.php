<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;
    protected $fillable = [
        'kpi_category_id',
        'name',
    ];

    public function kpi_category(){
        return $this->belongsTo('App\Models\KpiCategory');
    }
}
