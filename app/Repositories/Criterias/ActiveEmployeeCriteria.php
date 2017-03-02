<?php namespace App\Repositories\Criterias; 

use Prettus\Repository\Contracts\RepositoryInterface; 
use Prettus\Repository\Contracts\CriteriaInterface;


class ActiveEmployeeCriteria implements CriteriaInterface {

  public function apply($model, RepositoryInterface $repository)
  {
      $model = $model->where('empstatus', '<>', '4');
      return $model;
  }
}