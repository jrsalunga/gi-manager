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




  public function getMonth(Request $request) {

    $res = $this->setDateRangeMode($request, 'month');
    
    $dailysales = $this->ds->getMonth($request, $this->dr);

    if($res)
      return view('analytics.month')->with('dailysales', $dailysales)->with('dr', $this->dr);
    else {
      $request->session()->flash('alert-warning', 'Max months reached! Adjusted to '.$this->dr->fr->format('M Y').' - '.$this->dr->to->format('M Y'));
      return view('analytics.month')->with('dailysales', $dailysales)->with('dr', $this->dr);
    }
      

  }

  // modify the date on DateRange instanced based on the 'mode'
  private function setDateRangeMode(Request $request, $mode='day') { 

    switch ($mode) {
      case 'month':
        $to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : Carbon::now()->endOfMonth();
        $fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $to->copy()->subMonths(3)->startOfMonth();
        if ($to->lt($fr)) {
          $to = Carbon::now()->endOfMonth();
          $fr = $to->copy()->startOfMonth();
        } else {
          $to = $to->endOfMonth();
          $fr = $fr->startOfMonth();
        }
        break;
      default:
        $to = Carbon::now()->endOfMonth();
        $fr = $to->copy()->startOfMonth();
        break;
    }

    // if more than a year
    if($fr->diffInDays($to, false)>=731) { // 730 = 2yrs
      $this->dr->fr = $to->copy()->subDays(730)->startOfMonth();
      $this->dr->to = $to;
      $this->dr->date = $to;
      return false;
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
