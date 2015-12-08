<?php namespace App\Models;

use App\Models\BaseModel;

class Empphoto extends BaseModel {

  //protected $connection = 'mysql-hr';
	protected $table = 'empphoto';
 	protected $fillable = ['employeeid', 'image'];

	public function employee() {
    return $this->hasMany('App\Models\Employee', 'employeeid');
  }

  




  
}
