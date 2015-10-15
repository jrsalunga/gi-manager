<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

	public $timestamps = false;

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
