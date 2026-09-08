<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    
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
