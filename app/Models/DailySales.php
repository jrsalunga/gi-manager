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


  public function getOpex() {
    if(Carbon::parse($this->date->format('Y-m-d'))->lt(Carbon::parse('2017-01-01')))
      return 0;
    else
      return $this->purchcost - $this->cos;
  }

  public function get_opexpct($format=true) {
    if ($this->sales>0) {
      if ($format)
        return number_format(($this->getOpex()/$this->sales)*100, 2);
      else
        return ($this->getOpex()/$this->sales)*100;
    }
    return 0;
  }

  public function get_cospct($format=true) {
    if ($this->sales>0) {
      if ($format)
        return number_format(($this->cos/$this->sales)*100, 2);
      else
        return ($this->cos/$this->sales)*100;
    }
    return 0;
  }

  public function get_mancostpct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->mancost/$this->sales)*100, 2);
      else
        return ($this>-mancost/$this->sales)*100;
    }
    return 0;
  }

  public function get_tipspct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->tips/$this->sales)*100, 2);
      else
        return ($this->tips/$this->sales)*100;
    }
    return 0;
  }

  public function get_purchcostpct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->purchcost/$this->sales)*100, 2);
      else
        return ($this->purchcost/$this->sales)*100;
    }
    return 0;
  }

  public function get_receipt_ave($format=true) {
    if ($this->trans_cnt>0){
      if ($format)
        return number_format($this->sales/$this->trans_cnt, 2);
      else
        return $this->sales/$this->trans_cnt;
    }
    return 0;
  }

	
	
 

 
	
  
}