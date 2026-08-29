<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkUnit extends Model
{
    use HasFactory;
    protected $connection = 'simpeg_mysql';
    protected $fillable = [
        'name'
    ];

    public function employee(){
        return $this->hasOne('App\Models\Employee');
    }

    public function count_employee(){
        return $this->hasMany('App\Models\Employee');
    }

    public function count_employee_male(){
        return $this->hasMany('App\Models\Employee')->where('gender', 'Male');
    }

    public function count_employee_female(){
        return $this->hasMany('App\Models\Employee')->where('gender', 'Female');
    }

}
