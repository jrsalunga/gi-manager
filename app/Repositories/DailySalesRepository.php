<?php namespace App\Repositories;

use DB;
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



  private function MonthInterval(Carbon $fr, Carbon $to){
    $interval = new DateInterval('P6D');
    $to->add($interval);
    return new DatePeriod($fr, $interval, $to);
  }


  public function branchByDR($branchid, DateRange $dr, $order = 'ASC') {
    
    $arr = [];
    $dss = $this->scopeQuery(function($query) use ($order, $dr) {
              return $query->whereBetween('date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                            ->orderBy('date', $order);
          })->findWhere([
            'branchid' => $branchid
          ]);
    

    foreach ($dr->dateInterval() as $key => $date) {
      $filtered = $dss->filter(function ($item) use ($date){
        return $item->date->format('Y-m-d') == $date->format('Y-m-d')
              ? $item : null;
      });
      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }

    return collect($arr);

  }


  private function getAggregateByDateRange($fr, $to) {

    $sql = 'date, MONTH(date) AS month, YEAR(date) as year, SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(mancost) AS mancost,  ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('MONTH(date), YEAR (date)'))
        ->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }

  public function getMonth(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateByDateRange($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    foreach ($dr->monthInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return $item->date->format('Y-m') == $date->format('Y-m')
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }

  private function getAggregateWeekly($fr, $to) {

    $sql = 'date, MONTH(date) AS month, YEAR(date) as year, SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'WEEKOFYEAR(date) as week, YEARWEEK(date, 3) AS yearweak, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(mancost) AS mancost, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('YEARWEEK(date, 3)'));
        //->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }



  public function getWeek(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateWeekly($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    //return $dr->weekInterval();

    foreach ($dr->weekInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return $item->yearweak == $date->format('YW')
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }



  private function getAggregateQuarterly($fr, $to) {

    $sql = 'date, QUARTER(date) as quarter, YEAR(date) as year, SUM(sales) AS sales, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(mancost) AS mancost, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('YEAR(date), QUARTER(date)'));
        //->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }



  public function getQuarter(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateQuarterly($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    foreach ($dr->quarterInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return ($item->quarter == $date->quarter) && ($item->year == $date->year)
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }



  private function getAggregateYearly($fr, $to) {

    $sql = 'date, YEAR(date) as year, SUM(sales) AS sales, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(mancost) AS mancost, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to])
        ->groupBy(DB::raw('YEAR(date)'));
        //->orderBy(DB::raw('YEAR (date), MONTH(date)'));
    });

  }


  public function getYear(Request $request, DateRange $dr) {
    $arr = [];
    $data = $this->getAggregateYearly($dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d'))->all();

    foreach ($dr->yearInterval() as $key => $date) {

      $filtered = $data->filter(function ($item) use ($date){
        return ($item->year == $date->year)
          ? $item : null;
      });

      $obj = new StdClass;
      $obj->date = $date;
      $obj->dailysale = $filtered->first();
      $arr[$key] = $obj;
    }
    return collect($arr);
  }

  public function sumByDateRange($fr, $to) {

    $sql = 'SUM(sales) AS sales, SUM(crew_kit) AS crew_kit, SUM(crew_din) AS crew_din, ';
    $sql .= 'SUM(purchcost) AS purchcost, SUM(cos) AS cos, SUM(tips) AS tips, SUM(slsmtd_totgrs) AS slsmtd_totgrs, ';
    $sql .= 'SUM(custcount) AS custcount, SUM(empcount) AS empcount, SUM(headspend) AS headspend, branchid';

    return $this->scopeQuery(function($query) use ($fr, $to, $sql) {
      return $query->select(DB::raw($sql))
        ->whereBetween('date', [$fr, $to]);
    });

  }


}