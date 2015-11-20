<?php namespace App\Models;

use App\Models\BaseModel;

class Holiday extends BaseModel {

	protected $table = 'holiday';
 	protected $fillable = ['code', 'descriptor', 'type', 'isregional'];

	public function holidaydtls() {
    return $this->hasMany('App\Models\Holidaydtl', 'holidayid');
  }

  public function holidates() {
    return $this->hasMany('App\Models\Holidate', 'holidayid');
  }





  /***************** mutators *****************************************************/
  
}
