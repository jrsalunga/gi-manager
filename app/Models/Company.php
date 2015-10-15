<?php namespace App\Models;

use App\Models\BaseModel;

class Company extends BaseModel {

	protected $table = 'company';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  
}