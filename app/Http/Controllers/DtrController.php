<?php namespace App\Http\Controllers;

use DateInterval;
use DatePeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Dtr;
use App\Models\Manskeddtl as Mandtl;
use App\Models\Timelog;
use App\Models\Holidate;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\DtrRepository;
use App\Repositories\ManskeddtlRepository as MandtlRepo;

class DtrController extends Controller {

  public $dtr;
  public $dtrs;
    
  public function __construct(DtrRepository $dtrs) {
    $this->dtrs = $dtrs;
  }

  public function date($date, MandtlRepo $mandtls, Request $request) {
    return $mandtls->branchByDate($request->user(), $date);
  }

  public function getIndex(Request $request, $param1=null, $param2=null, $param3=null){
    if(strtolower($param1)==='generate')
      return $this->getGenerate($request);
    else if(is_year($param1) && is_null($param2) && is_null($param3)) 
      return $this->makeMonthsView($request, $param1);
    else if(is_year($param1) && is_month($param2) && is_null($param3)) 
      return $this->makeListView($request, $param1, $param2, $param3);
    else if(is_year($param1) && is_month($param2) && is_day($param3)) 
      return 'make day';
    else
      return redirect('/dtr/'.now('year').'/'.now('month'));//return $this->makeListView($request, $param1, $param2, $param3);
  }

  public function makeListView(Request $request, $param1, $param2, $param3){
    $mandtls_repo = new MandtlRepo;
    $fr = Carbon::create($param1, $param2, 1, 0, 0, 0);
    $to = Carbon::parse($fr->format('Y-m').'-'.$fr->daysInMonth);
    $arr = [];
    foreach($this->getDates($fr, $to) as $date){
      $arr[$date->format("Y-m-d")]['date'] = $date;
      $arr[$date->format("Y-m-d")]['mandtls'] = $mandtls_repo->branchByDate($request->user(), $date->format("Y-m-d"));
      $arr[$date->format("Y-m-d")]['dtrs'] = $this->dtrs->branchByDate($request->user(), $date->format("Y-m-d"));
    }

    return view('dtr.list')->with('dtrs', $arr);
  }

  public function makeMonthsView(Request $request, $param1) {

    $arr = [];
    for ($i=1; $i < 13; $i++) { 
      $date = $param1.'-'.pad($i,2).'-01';
      $arr[$i]['month'] = date('F', strtotime($date));
      $x = $this->dtrs->countByYearMonth($request->user(), $param1, $i);

     $arr[$i]['total'] = $x['total'];
    }
    
    return view('dtr.months')->with('months', $arr)->with('year', $param1);
  }





  public function getDtrReports(Request $request, $date){
    try { 
      $dt = Carbon::parse($date); 
    } catch(\Exception $x) { 
      $dt = Carbon::now(); 
      return redirect('/reports/dtr/'.$dt->format('Y-m-d'));
    }

    return view('dtr.view')->with('dtrs', $this->dtrs->branchByDate($request->user(), $dt->format('Y-m-d'))); 
  }

  public function getGenerate(Request $request) {
    return view('dtr.generate');
  }

  private function ajaxPostGenerate(Request $request){

    $this->validate($request, [
      'fr' => 'required|date',
      'to' => 'required|date',
    ]);
      
    $fr = Carbon::parse($request->fr);
    $to = Carbon::parse($request->to);

    if($to->lt($fr)) // if to < fr
      //return redirect('dtr/generate')->withErrors(['message'=>'Date From is greater than Date To!']);
      return response()->json(['status'=>'error', 'code'=>400, 'data'=>['message'=>'Date From is greater than Date To!'], 'alert'=>'alert-danger'])
                  ->setCallback($request->input('callback'));

    if($to->diffInDays($fr) > 31) // check in date range exceeded 1 mons
      //return redirect('dtr/generate')->withErrors(['message'=>'Date range too high!']);
    return response()->json(['status'=>'error', 'code'=>400, 'data'=>['message'=>'Date range too high!'], 'alert'=>'alert-danger'])
                  ->setCallback($request->input('callback'));

    $employees = Employee::branchid(session('user.branchid'))
                          ->processing()
                          ->orderBy('lastname', 'ASC')
                          ->orderBy('firstname', 'ASC')
                          ->get();

    foreach($this->getDates($fr, $to) as $date){      
      foreach ($employees as $employee) {
        
        $this->dtr = $this->dtrExistOrCreate($employee->id, $date->format('Y-m-d'));

        $this->setEmployeeMandtl($employee->id, $date->format('Y-m-d'));

        $this->setEmployeeTimelog($employee->id, $date);

        $this->checkDay($employee->branchid);

        $this->computeWorkHours();        
        
        $this->dtr->save();
      }
    }

    return response()->json(['status'=>'success', 'code'=>200, 'data'=>['message'=>'DTR generated!'], 'alert'=>'alert-success'])
                  ->setCallback($request->input('callback'));
  }

 
  public function postGenerate(Request $request) {

    if($request->ajax()){
                
      return $this->ajaxPostGenerate($request);
    }

    $this->validate($request, [
      'fr' => 'required|date',
      'to' => 'required|date',
    ]);
      
    $fr = Carbon::parse($request->fr);
    $to = Carbon::parse($request->to);

    if($to->lt($fr)) // if to < fr
      return redirect('dtr/generate')->withErrors(['message'=>'Date From is greater than Date To!']);

    if($to->diffInDays($fr) > 31) // check in date range exceeded 1 mons
      return redirect('dtr/generate')->withErrors(['message'=>'Date range too high!']);

    $employees = Employee::branchid(session('user.branchid'))
                          ->processing()
                          ->orderBy('lastname', 'ASC')
                          ->orderBy('firstname', 'ASC')
                          ->get();

    foreach($this->getDates($fr, $to) as $date){

      echo $date->format("Y-m-d").'<br>';
      
      foreach ($employees as $employee) {
        
        echo $employee->lastname.', '.$employee->firstname.'<br>';

        $this->dtr = $this->dtrExistOrCreate($employee->id, $date->format('Y-m-d'));

        $this->setEmployeeMandtl($employee->id, $date->format('Y-m-d'));

        $this->setEmployeeTimelog($employee->id, $date);

        $this->checkDay($employee->branchid);

        $this->computeWorkHours();

        echo 'dtr: '.$this->dtr->timein.' - '.$this->dtr->breakin.' - '.$this->dtr->breakout.' - '.$this->dtr->timeout.'<br>'; 
        echo  $this->dtr->lid().'<br>';
        
        $this->dtr->save();

       
        
      }
      echo '<hr>';
    }
  }

  // for $this->postGenerate()
  private function getDates($fr, $to){
    $interval = new DateInterval('P1D');
    $to->add($interval);
    return new DatePeriod($fr, $interval ,$to);
  }
  
  // for $this->postGenerate()
  private function dtrExistOrCreate($employeeid, $date){
    $dtr = Dtr::employeeid($employeeid)->date($date)->first();
      if(is_null($dtr)){
        $n = new Dtr;
        $n->date = $date;
        $n->employeeid = $employeeid;
        $n->id = $n->get_uid();
        return $n;
      }
    return $dtr;
  }

  // for $this->postGenerate()
  private function setEmployeeMandtl($employeeid, $date){

    $mandtl = Mandtl::employeeid($employeeid)->date($date)->first();
        
      if(count($mandtl) <= 0){
        //echo count($mandtl).'<br>';
        $this->dtr->daytype     = '2';
       
      } else {
        
        if($mandtl->daytype=='0') {
          $this->dtr->daytype     = '2';
          //echo 'off<br>';
        } else {
          //echo 'mandtl: '.$mandtl->daytype.' - '.$mandtl->timestart.' - '.$mandtl->breakstart.' - '.$mandtl->breakend.' - '.$mandtl->timeend.'<br>';
        }
          

        $this->dtr->daytype     = $mandtl->daytype;
        $this->dtr->timestart   = $mandtl->timestart;
        $this->dtr->breakstart  = $mandtl->breakstart;
        $this->dtr->breakend    = $mandtl->breakend;
        $this->dtr->timeend     = $mandtl->timeend;

        //echo 'dtr dtl: '.$this->dtr->daytype.' - '.$this->dtr->timestart->format('H:i').' - '.$this->dtr->breakstart->format('H:i').' - '.$this->dtr->breakend->format('H:i').' - '.$this->dtr->timeend->format('H:i').'<br>';
      }
  }

  // for $this->postGenerate() 
  private function empTlog($employeeid, $date, $i, $order){
    return Timelog::employeeid($employeeid)
                  //->date($date)
                  ->whereBetween('datetime', [
                      $date, // '2015-11-13 00:00:00'
                   // $date->copy()->addDay() // '2015-11-14 00:00:00'
                      $date->copy()->addDay()->format('Y-m-d'). ' 06:00:00' // '2015-11-14 06:00:00'
                    ])
                  ->txncode($i)
                  ->orderBy('datetime', $order)
                  ->first();
  }

  // for $this->postGenerate() 
  private function setEmployeeTimelog($employeeid, $date, $order='ASC'){

    $absent = true;
    for ($i=1; $i < 5; $i++) { 

      $t = $this->empTlog($employeeid, $date, $i, $order);
      if(is_null($t)){
        $u = ''; 
        //echo 'no timelog<br>';
      } else {
        $u = $t->datetime->format('H:i'); 
        $absent = false; 
        //echo 'absent false<br>';
      }
        


      switch ($i) {
        case 1:
          $this->dtr->timein = $u;
          break;
        case 2:
          $this->dtr->breakin = $u;
          break;
        case 3:
          $this->dtr->breakout = $u;
          break;
        case 4:
          $this->dtr->timeout = $u;
          break;
      }
    }

    //echo 'is absent? '.($absent && $this->dtr->daytype == 1).'<br>';
    if($absent && $this->dtr->daytype == 1)
      $this->dtr->isabsent = 1;
  }

  // for $this->postGenerate() 
  private function checkDay($branchid){

    $this->dtr->daytype = $this->isHoliday($this->dtr->daytype, $this->dtr->date, $branchid);
  }

  // for $this->checkDay()
  private function isHoliday($daytype, $date, $emp_branchid){

    $holidate = Holidate::with('holiday.holidaydtls')->date($date)->first();

    if(is_null($holidate) && $daytype == 1) // no holiday and work day
      return 1;
    else if(is_null($holidate) && $daytype == 2) // no holiday and rest day
      return 4;
    else
      return $this->checkHolidayType($holidate->holiday->type, $daytype); // holiday and check daytype
  }

  // for $this->isHoliday()
  private function checkHolidayType($type, $daytype){

    if($type == 1 && $daytype == 1) // is regular holiday and work day 
      return 2;
    else if($type == 2 && $daytype == 1) // is special holiday and work day 
      return 3;
    else if($type == 1 && $daytype == 2) // is regular holiday and rest day 
      return 5;
    else if($type == 2 && $daytype == 2) // is special holiday and rest day 
      return 6;
    else if($type == 1 && $daytype == 0) // is regular holiday and rest day 
      return 5;
    else if($type == 2 && $daytype == 0) // is special holiday and rest day 
      return 6;
  }

  // for $this->postGenerate() 
  private function computeWorkHours(){
    $who = Carbon::parse($this->dtr->date. '00:00:00');
    $wh = $who->copy();
    $work = $who->copy()->addHours(8);

    // if there is a pair of timein and time out; but incomplete breakin and breakout
    if(!$this->nt($this->dtr->timein) && !$this->nt($this->dtr->timeout) && 
    ($this->nt($this->dtr->breakin) || $this->nt($this->dtr->breakout))) {
      
      $wh->addMinutes($this->getMinDiff($dtr->timein, $dtr->timeout)); 
    } else {

       // if there is a pair of timein and breakin
      if(!$this->nt($this->dtr->timein) && !$this->nt($this->dtr->breakin)) 
        $wh->addMinutes($this->getMinDiff($this->dtr->timein, $this->dtr->breakin));
        
      // if there is a pair of breakout and timeout
      if(!$this->nt($this->dtr->breakout) && !$this->nt($this->dtr->timeout)) 
        $wh->addMinutes($this->getMinDiff($this->dtr->breakout, $this->dtr->timeout));
      
    }

    // check if late
    if(!$this->nt($this->dtr->timestart) && !$this->nt($this->dtr->timein)) {
      $late = $this->dtr->timestart->diffInMinutes($this->dtr->timein); 
      $this->dtr->tardyhrs = number_format(($late/60), 4);
    } else 
      $late = '';
    

    /*
    echo 'late: '. ($late/60).'<br>';
    echo 'work hours: '.($wh->diffInMinutes($who, false)/60).'<br>';
    //echo 'work hours: '.$wh->format('H:i').'<br>';
    echo 'working: '.$work->format('H:i').'<br>';
    echo 'OT: '.($work->diffInMinutes($wh, false)/60).'<br>';
    */

    $this->setHoursToWorkType($this->dtr->daytype, ($wh->diffInMinutes($who)/60), ($work->diffInMinutes($wh, false)/60));
  }

  // $this->computeWorkHours()
  private function getMinDiff(Carbon $time1, Carbon $time2){
    if($time2->lt($time1)) // if timeout is less than breakout
      $time2->addDay(); // add 1 day
    return $time2->diffInMinutes($time1);
  }

  // $this->computeWorkHours()
  private function nt($date){ // null time
    $date = $date instanceof Carbon ? $date->format('H:i') : $date;
    if($date=='' || is_null($date) || $date=='00:00' || empty($date))
      return true;
    else
      return false;
  }

  // $this->computeWorkHours()
  private function setHoursToWorkType($type, $work_hrs, $ot_hrs){
    $ot_hrs = $ot_hrs <= 0 ? '0.00':number_format($ot_hrs,4); 
    $work_hrs = $work_hrs <= 0 ? '0.00':number_format($work_hrs,4); 

    switch ($type) {
      case '2':
        $this->dtr->rhhrs   = $work_hrs;
        $this->dtr->rhothrs = $ot_hrs;
        break;
      case '3':
        $this->dtr->shhrs   = $work_hrs;
        $this->dtr->shothrs = $ot_hrs;
        break;
      case '4':
        $this->dtr->rdhrs   = $work_hrs;
        $this->dtr->rdothrs = $ot_hrs;
        break;
      case '5':
        $this->dtr->rdrhhrs   = $work_hrs;
        $this->dtr->rdrhothrs = $ot_hrs;
        break;
      case '6':
        $this->dtr->rdshhrs   = $work_hrs;
        $this->dtr->rdshothrs = $ot_hrs;
        break;
      default:
        $this->dtr->reghrs   = $work_hrs;
        $this->dtr->othrs    = $ot_hrs;
        break;
    }

  }


    
}
