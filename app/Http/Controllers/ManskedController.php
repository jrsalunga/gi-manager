<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Manskedhdr as Mansked;
use App\Models\Manskedday as Manday;
use App\Models\Manskeddtl as Mandtl;
use Auth;
use Carbon\Carbon;
use App\Repositories\ManskedhdrRepository as ManskedRepo;
use App\Repositories\EmployeeRepository as EmployeeRepo;
use App\Repositories\Filters\ByBranch;
use App\Repositories\Filters\Regular;
use App\Repositories\Filters\Male;
use App\Repositories\Filters\Female;
use App\Repositories\Filters\WithPosition;
use App\Repositories\Criterias\ActiveEmployeeCriteria as ActiveEmployee;

class ManskedController extends Controller {


	private $employees;
	private $manskeds;

	public function __construct(ManskedRepo $manskeds, EmployeeRepo $employees){
		$this->employees =  $employees;
		$this->employees->pushCriteria(new ActiveEmployee);
		$this->manskeds =  $manskeds;

	}

	public function getIndex(Request $request, $param1=null, $param2=null, $param3=null){
		if(strtolower($param1)==='add')
			return $this->makeAddView($request);
		else if(preg_match('/(20[0-9][0-9])/', $param1) && (strtolower($param2)==='week') && preg_match('/^[0-9]+$/', $param3)) //((strtolower($param1)==='week') && preg_match('/^[0-9]+$/', $param2)) 
			if ($request->has('print') && $request->input('print')=='true')
				return $this->makeViewWeekPrint($request, $param1, $param3);
			else
				return $this->makeViewWeek($request, $param1, $param3); //task/mansked/2016/week/7
		else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit')
			return $this->makeEditView($request, $param1);
		else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1))   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeSingleView($request, $param1);
		else
			return $this->makeListView($request, $param1, $param2);
	}

	//task/mansked/add
	public function makeAddView(Request $request) {
		$mansked = new Mansked;
		$branch = Branch::find($request->user()->branchid);
		$new = $mansked->newWeek($branch->id);
		$data = [
			'branch' => $branch->code.' - ' .$branch->descriptor,
			'branchid' => $branch->id,
			'manager' => $request->user()->name,
			'managerid' => $request->user()->id,
			'mancost' => $branch->mancost,
			'weekno' => $new['weekno'],
			'year' => $new['year']
		];
		return view('task.mansked.add')->with('data', $data);
	}

	//task/mansked
	public function makeListView(Request $request, $param1, $param2) {
		//return dd(app());
		/*
		$manskeds = Mansked::with('manskeddays')
													->where('branchid', $request->user()->branchid)
													->orderBy('year', 'DESC')
													->orderBy('weekno', 'DESC')
													->paginate('5');
		*/
		$manskeds = $this->manskeds->byBranchWithMandays($request);
								
		//return $this->manskeds->byBranch($request)->load('manskeddays');
													//->get();
		//return Carbon::now()->addYear()->year;

		//return $data->first();
		$data = $manskeds->paginate('5');
		$new = $this->manskeds->newWeek($request);

		/*
		if($data->count() <= 0){
			return $this->manskeds->newWeek($request);
			//return $this->$manskeds->test();
		} else {
			return $this->manskeds->newWeek($request);
			
		}
		*/
		//return $manskeds[0]->filledManday();
		//return $manskeds->newWeek($this->branchid);
		return view('task.mansked.list2')->with('manskeds', $data)->with('new', $new);

		//$weeks = Mansked::paginateWeeks($request, '2015', 5);
		//return view('task.mansked.list')->with('weeks', $weeks);
	}

	//task/mansked/week/{weekno}
	public function old_makeViewWeek($request, $year, $weekno){
		//$this->employees->pushFilters(new ByBranch($request));
		//$this->employees->pushFilters(new Regular());
		//$this->employees->pushFilters(new Male());
		//return $this->employees->all(['code', 'firstname', 'positionid']);
		//return $this->employees->paginate('5', ['code', 'firstname', 'positionid']);
		$depts = $this->employees->byDepartment($request);

		$mansked = Mansked::with('manskeddays')
											->where('weekno', $weekno)
											->where('year', $year)
											->where('branchid', $request->user()->branchid)
											->first();
  	//return $mansked;		
  	if(count($mansked) <= 0)
  		return redirect('/task/mansked');

  	$days = $mansked->manskeddays;
  	$manskeddays = [];
		for($h=0; $h<count($depts); $h++){
				$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
				for($i=0; $i<count($arr); $i++){
					for($j=0; $j<count($days); $j++){
						
						$manskeddays[$j]['date'] = $days[$j]->date;
						$manskeddays[$j]['id'] = strtolower($days[$j]->id);

						$mandtl = Mandtl::where('employeeid', $depts[$h]['employees'][$i]->id)
													->where('mandayid', $days[$j]->id)->get()->first();
						$manskeddays[$j]['mandtl'] = count($mandtl) > 0 ? $mandtl:
								['timestart'=>0, 'timeend'=>0, 'loading'=>0];
					}
					$depts[$h]['employees'][$i]['manskeddays'] = $manskeddays;
				}
			}
  	//return $depts;
  	return view('task.mansked.week2')->with('depts', $depts)->with('mansked', $mansked);
		//$manday = Mansked::getManskedday('2015', $weekno);
		//$mansked = Mansked::whereWeekno($weekno)->get()->first();
		//return view('task.mansked.week')->with('manday', $manday)->with('mansked', $mansked);
	}

	public function makeViewWeek($request, $year, $weekno) {

		$mansked = $this->manskeds
								->skipCache()
								->with('manskeddays.manskeddtls')
								->findWhere([
									'weekno'		=> $weekno,
									'year' 			=> $year,
									'branchid' 	=> $request->user()->branchid
								])->first();

		if (!$mansked)
			return abort('404');

		$mandtls = collect(array_collapse($mansked->manskeddays->pluck('manskeddtls')->toArray()));

		$empsOnMansked = $mandtls->pluck('employeeid')
														->unique()
														->values()
														->all();

		$depts = $this->employees->byDeptFrmEmpIds($empsOnMansked);

		if(count($mansked) <= 0)
  		return redirect('/task/mansked');

  	$days = $mansked->manskeddays;
  	$manskeddays = [];
		for($h=0; $h<count($depts); $h++) {
			$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
			for($i=0; $i<count($arr); $i++) {
				for($j=0; $j<count($days); $j++) {
					
					$manskeddays[$j]['date'] = $days[$j]->date;
					$manskeddays[$j]['id'] = strtolower($days[$j]->id);

					$mandtl = $mandtls
										->where('employeeid', $depts[$h]['employees'][$i]->id)
  									->where('mandayid', $days[$j]->id)
  									->first();

					$manskeddays[$j]['mandtl'] = count($mandtl) > 0 ? $mandtl:
							['timestart'=>0, 'timeend'=>0, 'loading'=>0];
				}
				$depts[$h]['employees'][$i]['manskeddays'] = $manskeddays;
			}
		}
		return view('task.mansked.week2')->with('depts', $depts)->with('mansked', $mansked);
	}

	public function makeViewWeekPrint($request, $year, $weekno){

		
		$mansked = $this->manskeds
								->with('manskeddays.manskeddtls')
								->findWhere([
									'weekno'		=> $weekno,
									'year' 			=> $year,
									'branchid' 	=> $request->user()->branchid
								])->first();

		$mandtls = collect(array_collapse($mansked->manskeddays->pluck('manskeddtls')->toArray()));

		$empsOnMansked = $mandtls->pluck('employeeid')
														->unique()
														->values()
														->all();

		$depts = $this->employees->byDeptFrmEmpIds($empsOnMansked);

		//$depts = $this->employees->byDepartment($request);

		/*
		$mansked = Mansked::with('manskeddays')
											->where('weekno', $weekno)
											->where('year', $year)
											->where('branchid', $request->user()->branchid)
											->first();
		*/
  	//return $mansked;		
  	if(count($mansked) <= 0)
  		return redirect('/task/mansked');


  	$days = $mansked->manskeddays;
  	$manskeddays = [];
		for($h=0; $h<count($depts); $h++) {
			$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
			for($i=0; $i<count($arr); $i++) {
				for($j=0; $j<count($days); $j++) {
					
					$manskeddays[$j]['date'] = $days[$j]->date;
					$manskeddays[$j]['id'] = strtolower($days[$j]->id);
					/*
					$mandtl = Mandtl::where('employeeid', $depts[$h]['employees'][$i]->id)
												->where('mandayid', $days[$j]->id)->get()->first();
					*/
					$mandtl = $mandtls
										->where('employeeid', $depts[$h]['employees'][$i]->id)
  									->where('mandayid', $days[$j]->id)
  									->first();
  				

					$manskeddays[$j]['mandtl'] = count($mandtl) > 0 ? $mandtl:
							['timestart'=>0, 'timeend'=>0, 'loading'=>0];
				}
				$depts[$h]['employees'][$i]['manskeddays'] = $manskeddays;
			}
		}
		
		//return $depts;
  	

  	return view('task.mansked.week-print')->with('depts', $depts)->with('mansked', $mansked);
	}

	public function testWeeks(Request $request) {
		$weeks = Mansked::paginateWeeks($request, '2015');
		return view('task.mansked.list')->with('weeks', $weeks);
	}

	






	public function post(Request $request){
		
		

		$this->validate($request, [
        //'date' => 'required|date|max:10',
        'weekno' => 'required',
        'year' => 'required',
    ]);

		 // check weekno if exist
		$mansked = Mansked::whereWeekno($request->input('weekno'))
												->where('year', $request->input('year'))
												->branchid($request->user()->branchid)
												->get();
		if(count($mansked) > 0){
			return redirect('/task/mansked/add')
                        ->withErrors(['message' => 'Week '. $request->input('weekno') .' of '.$request->input('year').' already created!'])
                        ->withInput();
		}


		if($request->input('weekno')>lastWeekOfYear($request->input('year'))) {
			return redirect('/task/mansked/add')
                        ->withErrors(['message' => 'Invalid week '. $request->input('weekno') .' of '.$request->input('year').'!'])
                        ->withInput();
		}



		/*
		$x = c(firstDayOfWeek($request->input('weekno'), $request->input('year'))->format('Y-m-d'));
		$days = [];
		for($c=0;$c<7;$c++) {
			array_push($days, $x->format('Y-m-d'));
			$x->addDay();
		}
		return $days;
		*/

		$x = firstDayOfWeek($request->input('weekno'), $request->input('year'));


		//$mansked = array_shift($mansked);
		$mansked = new Mansked;
		//return $mansked->getRefno();
		$mansked->refno 		= $mansked->getRefno();
		//$mansked->date 			= $request->input('date');
		$mansked->date 			= $x->format('Y-m-d');
		$mansked->weekno		= $request->input('weekno');
		$mansked->year			= $request->input('year');
		$mansked->branchid 	= $request->input('branchid');
		$mansked->managerid = $request->input('managerid');
		$mansked->mancost 	= $request->input('mancost');
		$mansked->notes 		= $request->input('notes');
		$mansked->updated_at= c();
		$mansked->id 				= $mansked->get_uid();

		$mandays = [];
		
    foreach ($mansked->getDaysByWeekNo($request->input('weekno')) as $key => $date) {
    		$manday = new Manday;
    		$manday->date = $date;
    		$manday->id = $manday->get_uid();
        array_push($mandays, $manday);
    }
    
    
    
    /*
    for($c=0;$c<7;$c++) {
    	$manday = new Manday;
  		$manday->date = $x->format('Y-m-d');
  		$manday->id = $manday->get_uid();
      array_push($mandays, $manday);
			
			$x->addDay();
			
		}
		*/
		
   

		\DB::beginTransaction(); //Start transaction!

    try {
      $mansked->save();
        try {
          $mansked->manskeddays()->saveMany($mandays);
        } catch(\Exception $e) {
          \DB::rollback();
          throw $e;
        }
    } catch(\Exception $e) {
      \DB::rollback();
      throw $e;
    }

    \DB::commit();

    return redirect('/task/mansked')->with(['new'=>true]);

		//$mansked->id
    //return $id;
    //return dd($mansked);
		$mansked->load('manskeddays');
		

		foreach ($mansked->manskeddays as $manskedday) {
			foreach ($this->employees as $employee) {
				$mandtl = new Mandtl;
				$mandtl->employeeid = $employee->id;
				$mandtl->id = $mandtl->get_uid();
				$manskedday->manskeddtls()->save($mandtl);
			}
		}

		//return $mansked;
		return redirect('/task/mansked')->with(['new'=>true]);
				
	}


	public function copyMansked(Request $request){

		$this->validate($request, [
        'lweekno' => 'required',
        'nweekno' => 'required',
        'year' => 'required',
        'lmanskedid' => 'required',
    ]);

    $mansked1 = Mansked::whereWeekno($request->input('nweekno'))
    								->where('year', $request->input('year'))
    								->where('branchid', $request->user()->branchid)
    								->get();
    
    if(count($mansked1) > 0){
			return redirect('/task/mansked')
                        ->withErrors(['message' => 'Manpower Schedule Week '. $request->input('nweekno') .' already exist!'])
                        ->withInput();
		}

    $mansked = Mansked::find($request->input('lmanskedid'));
		if(count($mansked) <= 0){
			return redirect('/task/mansked')
                        ->withErrors(['message' => 'Pointer Week '. $request->input('lweekno') .' not found!'])
                        ->withInput();
		}


		



    $new_mansked = new Mansked;
		//return $mansked->getRefno();
		$new_mansked->refno 		= $new_mansked->getRefno();
		$new_mansked->date 			= firstDayOfWeek($request->input('nweekno'), $request->input('year'));
		$new_mansked->weekno		= $request->input('nweekno');
		$new_mansked->year			= $request->input('year');
		$new_mansked->branchid 	= $mansked->branchid;
		$new_mansked->managerid = $mansked->managerid;
		$new_mansked->mancost 	= $mansked->mancost;
		$new_mansked->notes 		= 'copied from week '.$request->input('lweekno');
		$new_mansked->id 				= $mansked->get_uid();
	


		\DB::beginTransaction(); //Start transaction!


		try {
       	$new_mansked->save();
        try {
        		$this->createMandays($new_mansked, $mansked, $request);
        } catch(\Exception $e) {
          \DB::rollback();
          throw $e;
        }
    } catch(\Exception $e) {
      \DB::rollback();
      throw $e;
    }

    \DB::commit();

    return redirect('/task/mansked')->with(['new'=>true])
    				->with('alert-success', 'Week '.$request->input('lweekno').' successfully copied!');

    $new_mansked->load('manskeddays');

    $new_mandays = $new_mansked->manskeddays; 
    foreach ($new_mandays as $new_manday) {
    	$new_manday->load('manskeddtls');
    }

     return $new_mansked;
	}

	private function createMandays($mansked, $mansked_old, $request) {

		$mansked_old->load('manskeddays');
		$mandays = $mansked_old->manskeddays; 

    foreach ($mandays as $manday) {
    	$manday->load('manskeddtls');
    }

		
    foreach ($mansked->getDaysByWeekNo($request->input('nweekno'), $request->input('year')) as $key => $date) {
    		
    		$new_manday 						= new Manday;
    		$new_manday->date 			= $date->format('Y-m-d');
    		$new_manday->custcount 	= $mandays[$key]->custcount;
    		$new_manday->headspend	= $mandays[$key]->headspend;
    		$new_manday->empcount		= $mandays[$key]->empcount;
    		$new_manday->workhrs		= $mandays[$key]->workhrs;
    		$new_manday->breakhrs		= $mandays[$key]->breakhrs;
    		$new_manday->overload   = $mandays[$key]->overload;
    		$new_manday->underload  = $mandays[$key]->underload;
    		$new_manday->id 				= $new_manday->get_uid();


    		try {
        	$manday = $mansked->manskeddays()->save($new_manday);
        } catch(\Exception $e) {
          \DB::rollback();
          throw $e;
        }
        
        $new_mandtls = [];
        foreach ($mandays[$key]->manskeddtls as $mandtl) {
        	
        	$new_mandtl 						= new Mandtl;
        	$new_mandtl->employeeid = $mandtl->employeeid;
        	$new_mandtl->daytype 		= $mandtl->daytype;
        	$new_mandtl->timestart 	= $mandtl->timestart;
        	$new_mandtl->breakstart = $mandtl->breakstart;
        	$new_mandtl->breakend 	= $mandtl->breakend;
        	$new_mandtl->timeend 		= $mandtl->timeend;
        	$new_mandtl->workhrs 		= $mandtl->workhrs;
        	$new_mandtl->breakhrs 	= $mandtl->breakhrs;
        	$new_mandtl->loading 		= $mandtl->loading;
        	$new_mandtl->id 				= $new_mandtl->get_uid();

        	array_push($new_mandtls, $new_mandtl);
        }

        try {
        	$manday->manskeddtls()->saveMany($new_mandtls);
        } catch(\Exception $e) {
          \DB::rollback();
          throw $e;
        }
    }
	}









}


