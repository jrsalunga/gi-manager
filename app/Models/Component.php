<?php namespace App\Models;

use App\Models\BaseModel;

class Component extends BaseModel {

	protected $table = 'component';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  
}
