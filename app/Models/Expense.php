<?php namespace App\Models;

use App\Models\BaseModel;

class Expense extends BaseModel {

	protected $table = 'expense';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  
}
