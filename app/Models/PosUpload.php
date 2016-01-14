<?php namespace App\Models;


use App\Models\BaseModel;

class PosUpload extends BaseModel {


	protected $table = 'posbackup';
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