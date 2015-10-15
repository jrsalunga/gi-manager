<?php namespace App\Models;

use App\Models\BaseModel;

class Branch extends BaseModel {

	protected $table = 'branch';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	public function employee() {
    return $this->hasMany('App\Models\Employee', 'employeeid');
  }
  
}
