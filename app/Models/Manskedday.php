<?php namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

class Manskedday extends BaseModel {

	protected $table = 'manskedday';
	public $incrementing = false;
	public $timestamps = false;
 	protected $fillable = ['manskedid', 'date', 'custcount', 'headspend', 'empcount', 'workhrs', 'breakhrs', 'loading'];
 	//public static $header = ['code', 'descriptor'];



 	/***************** relations *****************************************************/
	public function manskedhdr() {
    return $this->belongsTo('App\Models\Manskedhdr', 'manskedid');
  }

  public function manskeddtls() {
    return $this->hasMany('App\Models\Manskeddtl', 'mandayid');
  }

  /***************** mutators *****************************************************/
  public function getDateAttribute($value){
      return Carbon::parse($value);
  }
  
}
