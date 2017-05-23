<?php namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;

class Timelog extends BaseModel {

	protected $table = 'gi-tk.timelog';
 	protected $fillable = ['employeeid', 'rfid', 'branchid', 'datetime', 'txncode', 'entrytype', 'terminal', 'createdate'];
 	public static $header = ['code', 'lastname'];
 	protected $casts = [
    'txncode' => 'integer',
    'entrytype' => 'integer'
  ];

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql');
    else  
    	$this->setConnection('tk-live');
  }

 	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }








/***************** query scope *****************************************************/
	



/***************** mutators *****************************************************/
  public function getDatetimeAttribute($value){
    return Carbon::parse($value);
  }

  public function getCreatedateAttribute($value){
    return Carbon::parse($value);
  }


/***************** misc functions *****************************************************/
  public function getTxnCode(){
  	switch ($this->txncode) {
			case 1:
				return 'Time In';
				break;
			case 2:
				return 'Break Start';
				break;
			case 3:
				return 'Break End';
				break;
			case 4:
				return 'Time Out';
				break;
			default:
				return '-';
				break;
		}
	}

	

	/*********   http://laravel.com/docs/eloquent#query-scopes    *******************/
	
	public function scopeEmployeeid($query, $employeeid) {
		return $query->whereEmployeeid($employeeid);
  }

  public function scopeDate($query, $date) {
  	$date = $date instanceof Carbon ? $date->format('Y-m-d') : $date;
    return $query->where('datetime', 'like', $date.'%');
  }
	
	public function scopeTxncode($query, $txncode) {
		return $query->whereTxncode($txncode);
  }
	
	public function scopeEntrytype($query, $entrytype) {
		return $query->whereEntrytype($entrytype);
  }
	
	public function scopeTerminalid($query, $terminalid) { 	
		return $query->whereTerminalid($terminalid);
  }
  
}
