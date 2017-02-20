<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

class Salesmtd extends BaseModel {

  protected $connection = 'boss';
	protected $table = 'salesmtd';
  protected $fillable = ['tblno', 'wtrno', 'ordno', 'product_id', 'qty', 'uprice', 'grsamt', 
                        'disc', 'netamt', 'orddate', 'ordtime', 'recno', 'cslipno', 'custcount', 'paxloc', 
                        'group', 'remarks', 'cashier', 'branch_id'];
	//protected $guarded = ['id'];
  //protected $appends = ['transdate'];
  protected $dates = ['orddate', 'ordtime'];
	protected $casts = [
    'qty' => 'float',
    'uprice' => 'float',
    'grsamt' => 'float',
    'disc' => 'float',
    'netamt' => 'float',
    'ordno' => 'integer',
    'recno' => 'integer',
    'cslipno' => 'integer',
    'custcount' => 'integer',
  ];


  public function branch() {
    return $this->belongsTo('App\Models\Branch');
  }

  public function product() {
    return $this->belongsTo('App\Models\Product');
  }

 



 



}
