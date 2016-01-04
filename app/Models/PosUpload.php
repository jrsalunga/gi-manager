<?php namespace App\Models;


use App\Models\BaseModel;

class PosUpload extends BaseModel {


	protected $table = 'posbackup';
	public $timestamps = false;
 	//protected $fillable = ['branchid', 'size', 'terminal', 'filename', 'remarks', 'userid', 'year', 'month', 'mimetype'];
	protected $guarded = ['id'];
 

 
	
  
}