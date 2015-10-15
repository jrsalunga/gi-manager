<?php namespace App\Models;

use App\Models\BaseModel;

class Religion extends BaseModel {

	protected $table = 'religion';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  
}