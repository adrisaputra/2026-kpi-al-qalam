<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    
    public function report(){
        return $this->hasOne('App\Models\Report');
    }

    public function reports(){
        return $this->hasMany('App\Models\Report');
    }

}
