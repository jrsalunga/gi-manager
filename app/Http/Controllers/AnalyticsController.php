<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\DailySalesRepository as DSRepo;

class AnalyticsController extends Controller
{
  protected $ds;
  protected $dr;

  public function __construct(DSRepo $dsrepo, DateRange $dr) {
    $this->ds = $dsrepo;
    $this->dr = $dr;
  }




  public function getDaily(Request $request){

    $res = $this->setDateRangeMode($request, 'daily');

    $dailysales = $this->ds->branchByDR($request->user()->branchid, $this->dr);

    return $this->setViewWithDR(view('dashboard.analytics')->with('dailysales', $dailysales));
  }


  public function getWeekly(Request $request) {

    $res = $this->setDateRangeMode($request, 'weekly');

    $dailysales = $this->ds->getWeek($request, $this->dr);

    if(!$res)
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));

    return $this->setViewWithDR(view('analytics.weekly')->with('dailysales', $dailysales));
  }




  public function getMonth(Request $request) {

    $res = $this->setDateRangeMode($request, 'month');
    
    $dailysales = $this->ds->getMonth($request, $this->dr);

    if($res)
      return $this->setViewWithDR(view('analytics.month')->with('dailysales', $dailysales));
    else {
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));
      return $this->setViewWithDR(view('analytics.month')->with('dailysales', $dailysales));
    }
      

  }



  public function getQuarter(Request $request) {

    $res = $this->setDateRangeMode($request, 'quarterly');
    
    $dailysales = $this->ds->getQuarter($request, $this->dr);

    if($res)
      return $this->setViewWithDR(view('analytics.quarter')->with('dailysales', $dailysales));
    else {
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));
      return $this->setViewWithDR(view('analytics.quarter')->with('dailysales', $dailysales));
    }
  }


  public function getYear(Request $request) {

    $res = $this->setDateRangeMode($request, 'yearly');
    
    $dailysales = $this->ds->getYear($request, $this->dr);

    if($res)
      return $this->setViewWithDR(view('analytics.year')->with('dailysales', $dailysales));
    else {
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));
      return $this->setViewWithDR(view('analytics.year')->with('dailysales', $dailysales));
    }
  }

  

  // modify the date on DateRange instanced based on the 'mode'
  private function setDateRangeMode(Request $request, $mode='day') { 
    $y=false;
    switch ($mode) {
      case 'month':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(5)->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfMonth();
          $fr = $to->copy()->subMonths(5)->startOfMonth(); //$to->copy()->startOfMonth();
        } else {
          $to = $to->endOfMonth();
          $fr = $fr->startOfMonth();
        }
        break;
      case 'daily':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now();
          $fr = $to->copy()->startOfMonth();
        }
        break;
      case 'weekly':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfWeek();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subWeeks(5)->startOfWeek();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfWeek();
          $fr = $to->copy()->subWeeks(5)->startOfWeek(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->endOfWeek();
          $fr = $fr->startOfWeek();
        }
        break;
      case 'quarterly':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfQuarter();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(11)->firstOfQuarter();
        if ($to->lt($fr)) {
          $to = Carbon::now()->lastOfQuarter();
          $fr = $to->copy()->subMonths(12)->firstOfQuarter(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->lastOfQuarter();
          $fr = $fr->firstOfQuarter();
        }
        break;
      case 'yearly':
        $y=true;
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->lastOfYear();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subYear()->firstOfYear();
        if ($to->lt($fr)) {
          $to = Carbon::now()->lastOfYear();
          $fr = $to->copy()->subYear()->firstOfYear(); //$to->copy()->startOfWeek();
        } else {
          $to = $to->lastOfYear();
          $fr = $fr->firstOfYear();
        }
        break;
      default:
        $to = Carbon::now()->endOfMonth();
        $fr = $to->copy()->startOfMonth();
        break;
    }
    

    if(!$y){
      
      // if more than a year
      if($fr->diffInDays($to, false)>=731) { // 730 = 2yrs
        $this->dr->fr = $to->copy()->subDays(730)->startOfMonth();
        $this->dr->to = $to;
        $this->dr->date = $to;
        return false;
      }
    }


    $this->dr->fr = $fr;
    $this->dr->to = $to;
    $this->dr->date = $to;
    return true;
  }


  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }
}
