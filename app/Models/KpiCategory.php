<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'name',
    ];
    
    public function kpi(){
        return $this->HasOne('App\Models\Kpi');
    }

    public function kpis(){
        return $this->hasMany('App\Models\Kpi');
    }

}
