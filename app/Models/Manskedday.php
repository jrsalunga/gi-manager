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

  /***************** over ride base model ******************************************/
  public function next($branchid=null){
    
    $res = $this->query()
      ->select('manskedday.*')
      ->join('manskedhdr', function($join){
                            $join->on('manskedday.manskedid', '=', 'manskedhdr.id')
                                ->where('manskedhdr.branchid', '=', session('user.branchid'));
                            })
      ->where('manskedday.date', '>', $this->date)
      ->orderBy('manskedday.date', 'ASC')->get()->first();

    return $res==null ? 'false':$res;
  }

  public function previous($branchid=null){
    
    $res = $this->query()
      ->select('manskedday.*')
      ->join('manskedhdr', function($join){
                            $join->on('manskedday.manskedid', '=', 'manskedhdr.id')
                                ->where('manskedhdr.branchid', '=', session('user.branchid'));
                            })
      ->where('manskedday.date', '<', $this->date)
      ->orderBy('manskedday.date', 'DESC')->get()->first();

    return $res==null ? 'false':$res;
  }


  /***************** misc func *****************************************************/
  public function custCount(){
    if($this->custcount=='0' || $this->custcount=='0.00' || empty($this->custcount))
      return '-';
    else 
      return number_format($this->custcount, 0);
  }

  public function headSpend(){
    if($this->headspend=='0' || $this->headspend=='0.00' || empty($this->headspend))
      return '-';
    else 
      return '&#8369; '. number_format($this->headspend, 2);
  }

  public function workHrs(){
    if($this->workhrs=='0' || $this->workhrs=='0.00' || empty($this->workhrs))
      return '-';
    else 
      return number_format($this->workhrs, 2) + 0;
  }

  public function empCount(){
    if($this->empcount=='0' || $this->empcount=='0.00' || empty($this->empcount))
      return '-';
    else 
      return number_format($this->empcount, 2) + 0;
  }

  public function loadings(){
    if($this->overload=='0' || $this->overload=='0.00' || empty($this->overload))
      $o = '-';
    else {
      $o = number_format($this->overload, 2) + 0;
      $o = '+'.$o.'</span>';
    }
      

    if($this->underload=='0' || $this->underload=='0.00' || empty($this->underload))
      $u = '-';
    else 
      $u = number_format($this->underload, 2) + 0;

    return '<span style="color:blue">'.$o.'</span> / <span style="color:red;">'.$u.'</span>';
  }

 

  public function computeMancost($branch_mancost=0, $formated=false){
    if(($this->custcount*$this->headspend) != 0){
      if($formated)
        return number_format((($this->empcount*$branch_mancost)/($this->custcount*$this->headspend)*100),2).' %'; 
      else
        return ($this->empcount*$branch_mancost)/($this->custcount*$this->headspend)*100;
    } else {
      if($formated)
        return '-';
      else
        return 0;
    }
  }

  public function computeHourcost($branch_mancost=0, $formated=false){
    if(($this->custcount*$this->headspend) != 0){
      if($formated)
        return number_format((($this->workhrs*($branch_mancost/8))/($this->custcount*$this->headspend)*100),2).' %'; 
      else
        return ($this->workhrs*($branch_mancost/8))/($this->custcount*$this->headspend)*100;
    } else {
      if($formated)
        return '-';
      else
        return 0;
    }
  }
}
