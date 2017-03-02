<?php namespace App\Models;

use App\Models\BaseModel;

class Department extends BaseModel {

	protected $table = 'department';
 	protected $fillable = ['code', 'descriptor'];
 	public static $header = ['code', 'descriptor'];

	
  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    if (app()->environment()==='production')
      $this->setConnection('mysql-hr');
      
     $this->setConnection('mysql-hr');
  }

  public function employee() {
    return $this->hasOne('App\Models\Employee', 'deptid');
  }
}