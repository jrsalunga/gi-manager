<?php namespace App\Models;

use App\Models\BaseModel;

class Holidate extends BaseModel {

	protected $table = 'holidate';
 	protected $fillable = ['date', 'holidayid'];

	public function holiday() {
    return $this->belongsTo('App\Models\Holiday', 'holidayid');
  }





  /***************** mutators *****************************************************/

  public function scopeDate($query, $date) {
    return $query->where('date', $date);
  }
  
}
