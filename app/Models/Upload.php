<?php namespace App\Models;

use App\Models\BaseModel;

class Upload extends BaseModel {

	protected $table = 'upload';
 	protected $fillable = ['employeeid', 'filename', 'filetype', 'terminal'];
 	public static $header = ['filename', 'filetype'];


 	public function employee() {
    return $this->belongsTo('App\Models\Employee', 'employeeid');
  }
	
}
