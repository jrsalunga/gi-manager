<?php namespace App\Models;

use App\Models\BaseModel;

class Compcat extends BaseModel {

	protected $table = 'compcat';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  
}
