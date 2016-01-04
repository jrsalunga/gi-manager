<?php namespace App\Models;


use App\Models\BaseModel;

class DailySales extends BaseModel {

	protected $connection = 'boss';
	protected $table = 'dailysales';
	public $timestamps = false;
 	//protected $fillable = ['date', 'branchid', 'managerid', 'sales', 'cos', 'tips', 'custcount', 'empcount'];
	protected $guarded = ['id'];
 

 
	
  
}