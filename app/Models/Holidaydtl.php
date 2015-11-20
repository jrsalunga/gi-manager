<?php namespace App\Models;

use App\Models\BaseModel;

class Holidaydtl extends BaseModel {

	protected $table = 'holidaydtl';
 	protected $fillable = ['holidayid', 'branchid'];

	public function holiday() {
    return $this->belongsTo('App\Models\Holiday', 'holidayid');
  }

  public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }





  /***************** mutators *****************************************************/
  
}
