<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $connection = 'auth_mysql';
    protected $fillable =[
        'name'
    ];
    
    public function group_application(){
        return $this->hasOne('App\Models\GroupApplication');
    }

}
