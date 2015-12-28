<?php namespace App\Repositories\Filters;

use App\Repositories\Filters\Filters;
use App\Repositories\Contracts\RepositoryInterface as Repository;
use Illuminate\Http\Request;

class Regular extends Filters {


    private $request;

    public function __construct(Request $request){
        $this->request = $request;
    }


    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->where('empstatus', '1');
        return $model;
    }
}