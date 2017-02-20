<?php namespace App\Repositories;

use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;


class BranchRepository extends BaseRepository implements CacheableInterface
//class BranchRepository extends BaseRepository 
{
  use CacheableRepository;

  protected $fieldSearchable = [
    'code'=>'like',
    'descriptor'=>'like',
    'company.descriptor'=>'like',
    'sectorid'=>'like',
  ];

	public function __construct() {
      parent::__construct(app());

			//$this->boots();      
  }

  public function boots() {
  	$this->scopeQuery(function($query){
  		return $query->whereNotIn('id', ['971077BCA54611E5955600FF59FBB323', '3C561250F87448E3A2DD0562B24E3639'])
  								->orderBy('code','asc');
		});
  }

  public function boot(){
    $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
  }

	public function model() {
    return 'App\\Models\\Branch';
  }



  public function active(){
    $this->getByCriteria(new ActiveBranch);
    return $this;
  }

  
 




  
  

    




}