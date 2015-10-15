<?php namespace App\Models;

use App\Models\BaseModel;

class Supplier extends BaseModel {

	protected $table = 'supplier';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  
}
