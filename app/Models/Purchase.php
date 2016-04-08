<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

class Purchase extends BaseModel {

  protected $connection = 'boss';
	protected $table = 'tpurchase';
	protected $guarded = ['id'];
	protected $casts = [
    'qty' => 'float',
    'ucost' => 'float',
    'tcost' => 'float',
    'vat' => 'float'
  ];


  public function getDateAttribute($value){
    return Carbon::parse($value);
  }
 

 
	
  
}