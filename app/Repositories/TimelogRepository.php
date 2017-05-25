<?php namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Timelog;
use Illuminate\Http\Request;
use App\Repositories\DateRange;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Criterias\ByBranchCriteria;
use Illuminate\Container\Container as App;
use App\Repositories\EmployeeRepository;
use App\Helpers\Timesheet;

class TimelogRepository extends BaseRepository 
{ 

  private $emprepo;

  public function __construct(EmployeeRepository $emprepo) {
    parent::__construct(app());

    $this->employees = $emprepo;

    $this->pushCriteria(new ByBranchCriteria(request()))
      ->scopeQuery(function($query){
        return $query->orderBy('datetime','desc');
      });

    
  }

  function model() {
    return "App\\Models\\Timelog";
  }


  public function allTimelogByDate(Carbon $date) {

    return $this->scopeQuery(function($query) use ($date){
        return $query->whereBetween('datetime', [
                      $date->copy()->format('Y-m-d').' 06:00:00',          // '2015-11-13 06:00:00'
                      $date->copy()->addDay()->format('Y-m-d').' 05:59:59' // '2015-11-14 05:59:59'
                    ])
                  ->where('branchid', session('user.branchid'))
                  ->where('ignore', 0)
                  ->orderBy('datetime', 'ASC')
                  ->orderBy('txncode', 'ASC');
      });
  }

  public function generateTimesheet($employeeid, Carbon $date, $timelogs) {
    $ts = new Timesheet;
    return $ts->generate($employeeid, $date, $timelogs);
  }

  public function getActiveEmployees($field = NULL) {
    $field = !is_null($field) ? $field : ['code', 'lastname', 'firstname','gender','empstatus','positionid','deptid','branchid','id'];
    return $this->employees->with('position')
                ->orderBy('lastname')
                ->orderBy('firstname')
                ->findWhereNotIn('empstatus', [4, 5], $field);
  }

  public function allByDate(Carbon $date) {

    $arr = [];
    $timelogs = [];
    // get all timelog on the day/date
    $raw_timelogs = $this->allTimelogByDate($date)->all();
    //$raw_timelogs = ;
    $tk_empids =  $raw_timelogs->pluck('employeeid')->toArray();
    $employees = $this->getActiveEmployees();

    $br_empids = $employees->pluck('id')->toArray();
    $combined_empids = collect($tk_empids)->merge($br_empids)->unique()->values()->all();

    $o = [];
    foreach ($combined_empids as $key => $id) {
      $o[$key] = $this->employees
            ->skipCriteria()
            ->findByField('id', $id, ['code', 'lastname', 'firstname', 'id'])
            ->first()->toArray();
    }

    $sorted_emps = collect($o)->sortBy('firstname')->sortBy('lastname');

    $col = collect($raw_timelogs);
    foreach (array_values($sorted_emps->toArray()) as $key => $emp) {
     
      $e = $this->employees
            ->skipCriteria()
            ->with(['position'])
            ->findByField('id', $emp['id'], ['code', 'lastname', 'firstname', 'id', 'positionid'])
            ->first();
      
      $arr[0][$key]['employee'] = $e;
      $arr[0][$key]['onbr'] = in_array($emp['id'], $br_empids) ? true : false; // on branch??

      for ($i=1; $i < 5; $i++) { 
        $c = $col->where('employeeid', $emp['id'])
                                            ->where('txncode', $i)
                                            ->sortBy('datetime');
                                            
        $arr[0][$key]['counts'][$i] = count($c);
        $arr[0][$key]['timelogs'][$i] = $c->first();
      }
      
      $raw = $raw_timelogs->where('employeeid', $e->id)->sortBy('datetime');
      
      $arr[0][$key]['timesheet'] = $this->generateTimesheet($e->id, $date, $raw);

      $arr[0][$key]['raw'] = $raw;
    }
    $arr[1] = [];
    return $arr;



    $employees = $this->employees->with('position')
                      ->all(['code', 'lastname', 'firstname','gender','empstatus','positionid','deptid','branchid','id']);
    
    // timelog of employee assign to this branch
    $timelogs[0] = $raw_timelogs->filter(function ($item) use ($employees) {
      if(in_array($item->employeeid, $employees->pluck('id')->toArray()))
        return $item; 
    });

    $timelogs[1] = $raw_timelogs->filter(function ($item) use ($employees) {
      if(!in_array($item->employeeid, $employees->pluck('id')->toArray()))
        return $item; 
    });

    $col = collect($timelogs[0]);

    
    if(count($employees)==0);
      $arr[0] = [];


    foreach ($employees as $key => $employee) {

      $arr[0][$key]['employee'] = $employee;
      
      for ($i=1; $i < 5; $i++) { 
        
        $arr[0][$key]['timelogs'][$i] = $col->where('employeeid', $employee->id)
                                        ->where('txncode', $i)
                                        ->sortBy('datetime')->first();
      }
      $arr[0][$key]['raw'] = $timelogs[0]->where('employeeid', $employee->id)
                            ->sortBy('datetime');
    }
    $arr[1] = $timelogs[1];


    return $arr;
    /*
    $filtered = $dss->filter(function ($item) use ($date){
          return $item->date->format('Y-m-d') == $date->format('Y-m-d')
                ? $item : null;
    */
  }



    /**
     * Get all the timelog for an employee on the day.
     *
     * @param  Employee $employee, Carbon $date ('Y-m-d')
     * @return Collection of timelog
     */
    public function employeeTimelogs(Employee $employee, $date)
    {
        $res = Timelog::employeeid($employee->id)
                  ->whereBetween('datetime', [
                      $date->copy()->format('Y-m-d').' 06:00:00',          // '2015-11-13 06:00:00'
                      $date->copy()->addDay()->format('Y-m-d').' 05:59:59' // '2015-11-14 05:59:59'
                    ])
                  ->orderBy('datetime', 'ASC')
                  ->orderBy('txncode', 'ASC')
                  ->get();
        return count($res)>0 ? $res:false;
    }

    public function getRawEmployeeTimelog($employeeid, Carbon $fr, Carbon $to) {

      return $this->scopeQuery(function($query) use ($employeeid, $fr, $to) {
        return $query->where('employeeid', $employeeid)
                    ->whereBetween('datetime', [
                      $fr->copy()->format('Y-m-d').' 06:00:00',          // '2015-11-13 06:00:00'
                      $to->copy()->addDay()->format('Y-m-d').' 05:59:59'
                    ]);
      });
  }
}