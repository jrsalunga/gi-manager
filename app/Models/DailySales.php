<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

class DailySales extends BaseModel {

	protected $connection = 'boss';
	protected $table = 'dailysales';
	public $timestamps = false;
 	//protected $fillable = ['date', 'branchid', 'managerid', 'sales', 'cos', 'tips', 'custcount', 'empcount'];
	protected $guarded = ['id'];
	protected $casts = [
    'sales' => 'float',
    'cos' => 'float',
    'tips' => 'float',
    'custcount' => 'integer',
    'empcount' => 'integer',
    'headspend' => 'float',
    'tipspct' => 'float',
    'mancostpct' => 'float',
    'cospct' => 'float',
    'purchcost' => 'float',
    'tot' => 'float',
    'slsmtd_totgrs' => 'float'
  ];


	public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }

  public function getDateAttribute($value){
    return Carbon::parse($value.' 00:00:00');
  }

  public function getSlsmtdTotgrsAttribute($value){
    if (Carbon::parse('2017-01-01')->gt(Carbon::parse($this->date)))
      return $this->sales;
    return $value;
  }

	
	
 

 
	
  
}