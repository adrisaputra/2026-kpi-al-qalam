<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'report_category_id',
        'name',
        'is_special_value'
    ];

    public function report_category(){
        return $this->belongsTo('App\Models\ReportCategory');
    }

}
