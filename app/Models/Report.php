<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $connection = 'mysql';
    protected $fillable = [
        'report_category_id',
        'name',
        'category',
        'is_special_value',
        'manual_input',
        'file_input',
        'jp'
    ];

    public function report_category(){
        return $this->belongsTo('App\Models\ReportCategory');
    }

}
