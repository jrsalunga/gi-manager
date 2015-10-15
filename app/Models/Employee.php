<?php namespace App\Models;

use App\Models\BaseModel;

class Employee extends BaseModel {

  protected $connection = 'mysql';
	protected $table = 'employee';
 	protected $fillable = ['code', 'lastname', 'firstname', 'middlename', 'positionid', 'branchid', 'punching', 'processing'];
 	public static $header = ['code', 'lastname'];
  public $timestamps = false;


 	public function timelogs() {
    return $this->hasMany('App\Models\Timelog', 'employeeid');
  }

  public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }

  public function position() {
    return $this->belongsTo('App\Models\Position', 'positionid');
  }

  public function uploads() {
    return $this->hasMany('App\Models\Upload', 'employeeid');
  }

  public function manskeddtls() {
    return $this->hasMany('App\Models\Manskeddtl', 'employeeid');
  }

  public function manskedhdr() {
    return $this->hasMany('App\Models\Manskedhdr', 'managerid');
  }




   /**
     * Query Scope.
     *
     */
   // Employee::Branchid('1')->get()
    public function scopeBranchid($query, $id)
    {
        return $query->where('branchid', $id);
    }
	
}
