<?php namespace App\Models;

use App\Models\BaseModel;

class Manskeddtl extends BaseModel {

	protected $table = 'manskeddtl';
	public $incrementing = false;
	public $timestamps = false;	
 	protected $fillable = ['mandayid', 'employeeid', 'daytype', 'timestart', 'breakstart', 'breakend', 'timeend', 'workhrs', 'breakhrs', 'loading'];
 	//public static $header = ['code', 'descriptor'];

	public function manskedday() {
    return $this->belongsTo('App\Models\Manskedday', 'mandayid');
  }

  public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }
  
}
