<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

class Backup extends BaseModel {

	protected $connection = 'boss';
	protected $table = 'backup';
	public $timestamps = false;
 	//protected $fillable = ['branchid', 'size', 'terminal', 'filename', 'remarks', 'userid', 'year', 'month', 'mimetype'];
	protected $guarded = ['id'];

	public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }

  public function getUploaddateAttribute($value){
    return Carbon::parse($value);
  }
 

 
	
  
}