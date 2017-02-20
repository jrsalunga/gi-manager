<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

class Purchase2 extends BaseModel {

  protected $connection = 'boss';
	protected $table = 'purchase';
	protected $guarded = ['id'];
	protected $dates = ['date'];
	protected $casts = [
    'qty' => 'float',
    'ucost' => 'float',
    'tcost' => 'float',
    'vat' => 'float',
  ];

  
  public function component() {
    return $this->belongsTo('App\Models\Component', 'componentid');
  }

  public function supplier() {
    return $this->belongsTo('App\Models\Supplier', 'supplierid');
  }

  public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }
 

 
	
  
}