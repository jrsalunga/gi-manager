<?php namespace App\Models;

use App\Models\BaseModel;

class Department extends BaseModel {

	protected $table = 'department';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  
}