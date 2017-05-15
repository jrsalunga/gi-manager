<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

class DailySales extends BaseModel {

	protected $connection = 'boss';
	protected $table = 'dailysales';
	public $timestamps = false;
 	//protected $fillable = ['date', 'branchid', 'managerid', 'sales', 'cos', 'tips', 'custcount', 'empcount'];
  protected $dates = ['opened_at', 'closed_at'];
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
    'mancost' => 'float',
    'salesemp' => 'float',
    'chrg_total' => 'float',
    'chrg_csh' => 'float',
    'chrg_chrg' => 'float',
    'chrg_othr' => 'float',
    'bank_totchrg' => 'float',
    'disc_totamt' => 'float',
    'slsmtd_totgrs' => 'float',
    'tot' => 'float'
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


  public function targetSales(){
    return $this->target_cust*$this->target_headspend;
  }

	
	
 

 
	
  
}