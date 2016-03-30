<?php namespace App\Repositories;

use StdClass;
use DateTime;
use DateInterval;
use DatePeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\DailySales;
#use App\Models\Branch;
use App\Repositories\DateRange;
use Prettus\Repository\Eloquent\BaseRepository;
#use App\Repositories\BossBranchRepository as BBRepo;
use App\Repositories\Criterias\ByBranchCriteria;
#use App\Repositories\Criterias\ActiveBranchCriteria as ActiveBranch;
#use App\Repositories\Criterias\BranchDailySalesCriteria;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;


class DailySalesRepository extends BaseRepository {

	public function __construct(App $app, Collection $collection, Request $request) {
    parent::__construct($app);

    $this->pushCriteria(new ByBranchCriteria($request))
  		->scopeQuery(function($query){
  			return $query->orderBy('date','desc');
			});

    
  }

  function model() {
    return "App\\Models\\DailySales";
  }


  public function getLastestSales(Request $request, $day=1) {
  	$arr = [];
  	$to = Carbon::now();
  	//$to = Carbon::parse('2016-03-6');
  	$fr = $to->copy()->subDay($day);
  	

  	$dss = DailySales::whereBetween('date', [$fr->format('Y-m-d'), $to->format('Y-m-d')])
  										->where('branchid', $request->user()->branchid)->get();


  	foreach ($this->dateInterval($fr, $to) as $key => $date) {
      $filtered = $dss->filter(function ($item) use ($date){
          return $item->date->format('Y-m-d') == $date->format('Y-m-d')
                ? $item : null;
      });
      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }

    return array_reverse($arr);
  }



  private function dateInterval(Carbon $fr, Carbon $to){
    $interval = new DateInterval('P1D');
    $to->add($interval);
    return new DatePeriod($fr, $interval, $to);
  }


}