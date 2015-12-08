<?php namespace App\Models;

use App\Models\BaseModel;

class Empdoc extends BaseModel {

  //protected $connection = 'mysql-hr';
	protected $table = 'empdoc';
 	protected $fillable = ['employeeid', 'image'];

	public function employee() {
    return $this->hasMany('App\Models\Employee', 'employeeid');
  }

  




  
}
