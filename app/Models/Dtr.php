<?php namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

class Dtr extends BaseModel {

	protected $table = 'dtr';
 	//protected $fillable = ['code', 'descriptor'];
 	//public static $header = ['code', 'descriptor'];

	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }





  /***************** mutators *****************************************************/
  // off because of the setting the mandtl on frontend @ url:/task/manday/{id}/edit 
  private function isOff($x) {
    return  ($x=='off' || empty($x) || is_null($x)) ? '' : $x;
  }

  public function setTimestartAttribute($value){
      return $this->attributes['timestart'] = $this->isOff($value);
  }

  public function setBreakstartAttribute($value){
      return $this->attributes['breakstart'] = $this->isOff($value);
  }

  public function setBreakendAttribute($value){
      return $this->attributes['breakend'] = $this->isOff($value);
  }

  public function setTimeendAttribute($value){
      return $this->attributes['timeend'] = $this->isOff($value);
  }

  public function getTimestartAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }

  public function getBreakstartAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }

  public function getBreakendAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }

  public function getTimeendAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }

  public function getTimeinAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }

  public function getBreakinAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }

  public function getBreakoutAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }

  public function getTimeoutAttribute($value){
      return Carbon::parse($this->date->format('Y-m-d').' '.$value);
  }
  
  public function getDateAttribute($value){
      return Carbon::parse($value.' 00:00:00');
  }
  



  /*********   http://laravel.com/docs/eloquent#query-scopes    *******************/
  
  public function scopeEmployeeid($query, $employeeid) {
    return $query->whereEmployeeid($employeeid);
  }

  public function scopeDate($query, $date) {
    $date = $date instanceof Carbon ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date));
    return $query->where('date', 'like', $date.'%');
  }

  public function getDayType(){
    return $this->daytype>6 || $this->daytype<1 ? '':config('gi-dtr.daytype')[$this->daytype];
  }


  public function totworkhrs(){
    switch ($this->daytype) {
      case '2':
        return $this->rhhrs;
        break;
      case '3':
        return $this->shhrs;
        break;
      case '4':
        return $this->rdhrs;
        break;
      case '5':
        return $this->rdrhhrs;
        break;
      case '6':
        return $this->rdshhrs;
        break;
      default:
        return $this->reghrs;
        break;
    }
  }

  public function workhrs(){
    if($this->totworkhrs() > 8)
      return 8;
    else if($this->totworkhrs() > 0 && $this->totworkhrs() < 8)
      return 8 - $this->totworkhrs();
    else
      return 0;
  }

  public function othrs(){
    switch ($this->daytype) {
      case '2':
        return $this->rhothrs;
        break;
      case '3':
        return $this->shothrs;
        break;
      case '4':
        return $this->rdothrs;
        break;
      case '5':
        return $this->rdrhothrs;
        break;
      case '6':
        return $this->rdshothrs;
        break;
      default:
        return $this->regothrs;
        break;
    }
  }


 
  
}
