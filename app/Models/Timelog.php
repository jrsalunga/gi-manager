<?php namespace App\Models;

use App\Models\BaseModel;

class Timelog extends BaseModel {

	protected $table = 'timelog';
 	protected $fillable = ['employeeid', 'datetime', 'txncode', 'entrytype', 'terminal'];
 	public static $header = ['code', 'lastname'];


 	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }
	
}
