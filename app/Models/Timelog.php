<?php namespace App\Models;

use App\Models\BaseModel;

class Timelog extends BaseModel {

	protected $table = 'timelog';
 	protected $fillable = ['employeeid', 'datetime', 'txncode', 'entrytype', 'terminal'];
 	public static $header = ['code', 'lastname'];


 	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }

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
}
