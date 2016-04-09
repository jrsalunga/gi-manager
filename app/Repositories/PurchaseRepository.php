<?php namespace App\Repositories;

use App\Repositories\Criterias\ByBranchCriteria;

use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Container\Container as App;

class PurchaseRepository extends BaseRepository 
{

	public function __construct() {
    parent::__construct(app());

    $this->pushCriteria(new ByBranchCriteria(request()));
    $this->scopeQuery(function($query){
		  return $query->orderBy('comp','asc');
		});
  }


	public function model() {
    return 'App\Models\Purchase';
  }



  


}