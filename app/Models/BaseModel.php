<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

	public $timestamps = false;
	public $year = '';
	

	public function __construct(){
		$this->year = date('Y', strtotime('now'));
		
	}

	public static function get_uid(){
		$id = \DB::select('SELECT UUID() as id');
		$id = array_shift($id);
		return strtoupper(str_replace("-", "", $id->id));
	}

	public function getUuid(){
		return strtoupper(md5(uniqid()));
	}


	public function next($fields = ['id']) {
		$class = get_called_class();
		$res = $class::where('id', '>', $this->id)->orderBy('id', 'ASC')->get($fields)->first();
		return !empty($res) ? $res : 'false';
	}

	public function previous($fields = ['id']) {
		$class = get_called_class();
		$res = $class::where('id', '<', $this->id)->orderBy('id', 'DESC')->get($fields)->first();
		return !empty($res) ? $res : 'false';
	}

	public function lid(){
		return strtolower($this->id);
	}

	public function nextByField($field = 'id'){
		$res = $this->query()->where($field, '>', $this->{$field})->orderBy($field, 'ASC')->get()->first();
		return $res==null ? 'false':$res;
	}

	public function previousByField($field = 'id'){
		$res = $this->query()->where($field, '<', $this->{$field})->orderBy($field, 'DESC')->get()->first();
		return $res==null ? 'false':$res;
	}

	public function firstRecord($field = 'id'){
		$res = $this->query()->orderBy($field, 'ASC')->get()->first();
		return $res==null ? 'false':$res;
	}

	public function lastRecord($field = 'id'){
		$res = $this->query()->orderBy($field, 'DESC')->get()->first();
		return $res==null ? 'false':$res;
	}


	

	


	public function getDaysByWeekNo($weekno='', $year=''){
  	$weekno = (empty($weekno) || $weekno > $this->lastWeekOfYear()) ? date('W', strtotime('now')) : $weekno;
  	$year = empty($year) ?  $this->year : $year;
		for($day=1; $day<=7; $day++) {
		    $arr[$day-1] = date('Y-m-d', strtotime($year."W".str_pad($weekno,2,'0',STR_PAD_LEFT).$day));
		}
		return $arr;
  }

	public function lastWeekOfYear($year='') {
		$year = empty($year) ? date('Y', strtotime('now')):$year;
    $date = new \DateTime;
    $date->setISODate($year, 53);
    return ($date->format("W") === "53" ? 53 : 52);
	}



	public static function getLastDayLastWeekOfYear($year=""){
			
			$year = empty($year) ?  date('Y', strtotime('now')) : $year;
			$day = 31;
			$init_weekno = date("W", mktime(0,0,0,12,$day,$year));
			//echo $init_weekno.'<br>';

			$weekno = 0;
			while ($init_weekno == '01') {
				$weekno = $init_weekno;
				$init_weekno = date("W", mktime(0,0,0,12,$day,$year));
				//echo '12/'.$day.'/'.$year.'<br>';
				$day--;
			}
			$weekno = date("W", strtotime($year.'-12-'.$day));
			return ['date' => $year.'-12-'.$day, 'weekno' => $weekno];
		}


	public function getRefno($len = 8){
 		return str_pad((intval(\DB::table($this->table)->max('refno')) + 1), $len, '0', STR_PAD_LEFT);
 	}
	
}
