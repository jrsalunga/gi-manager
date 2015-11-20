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

class ManskedController extends Controller {


	protected $branchid = '';
	protected $employees = [];

	public function __construct(){
		$this->branchid = Auth::user()->branchid;
		$this->employees = Employee::select('id')->where('branchid', $this->branchid)
															->where('empstatus','>','0')
															->get();
	}

	public function getIndex(Request $request, $param1=null, $param2=null, $param3=null){
		if(strtolower($param1)==='add')
			return $this->makeAddView($request);
		else if(preg_match('/(20[0-9][0-9])/', $param1) && (strtolower($param2)==='week') && preg_match('/^[0-9]+$/', $param3)) //((strtolower($param1)==='week') && preg_match('/^[0-9]+$/', $param2)) 
			return $this->makeViewWeek($param1, $param3);
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
		$branch = Branch::find(Auth::user()->branchid);
		$new = $mansked->newWeek($branch->id);
		$data = [
			'branch' => $branch->code.' - ' .$branch->descriptor,
			'branchid' => $branch->id,
			'manager' => Auth::user()->name,
			'managerid' => Auth::user()->id,
			'mancost' => $branch->mancost,
			'weekno' => $new['weekno'],
			'year' => $new['year']
		];
		return view('task.mansked.add')->with('data', $data);
	}

	//task/mansked
	public function makeListView(Request $request, $param1, $param2) {
		//return dd(app());
		$manskeds = Mansked::with('manskeddays')
													->where('branchid', $this->branchid)
													->orderBy('year', 'DESC')
													->orderBy('weekno', 'DESC')
													->paginate('5');
													//->get();
		//return Carbon::now()->addYear()->year;
		if($manskeds->count() <= 0){
			$manskeds = new Mansked;
			$new = $manskeds->newWeek($this->branchid);
		} else {
			$new = $manskeds[0]->newWeek($this->branchid);
		}
		//return $manskeds[0]->filledManday();
		//return $manskeds->newWeek($this->branchid);
		return view('task.mansked.list2')->with('manskeds', $manskeds)->with('new', $new);

		//$weeks = Mansked::paginateWeeks($request, '2015', 5);
		//return view('task.mansked.list')->with('weeks', $weeks);
	}

	//task/mansked/week/{weekno}
	public function makeViewWeek($year, $weekno){

		$depts = $this->empGrpByDept();

		$mansked = Mansked::with('manskeddays')->where('weekno', $weekno)
													->where('year', $year)
  												->where('branchid', Auth::user()->branchid)
  												->get()->first();
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

	public function testWeeks(Request $request) {
		$weeks = Mansked::paginateWeeks($request, '2015');
		return view('task.mansked.list')->with('weeks', $weeks);
	}

	






	public function post(Request $request){
		
		$this->validate($request, [
        'date' => 'required|date|max:10',
        'weekno' => 'required',
    ]);

		 // check weekno if exist
		$mansked = Mansked::whereWeekno($request->input('weekno'))
												->branchid(Auth::user()->branchid)
												->get();
		if(count($mansked) > 0){
			return redirect('/task/mansked/add')
                        ->withErrors(['message' => 'Week '. $request->input('weekno') .' already created!'])
                        ->withInput();
		}

		//$mansked = array_shift($mansked);
		$mansked = new Mansked;
		//return $mansked->getRefno();
		$mansked->refno 		= $mansked->getRefno();
		$mansked->date 			= $request->input('date');
		$mansked->weekno		= $request->input('weekno');
		$mansked->year			= $request->input('year');
		$mansked->branchid 	= $request->input('branchid');
		$mansked->managerid = $request->input('managerid');
		$mansked->mancost 	= $request->input('mancost');
		$mansked->notes 		= $request->input('notes');
		$mansked->id 				= $mansked->get_uid();

		$mandays = [];
    foreach ($mansked->getDaysByWeekNo($request->input('weekno')) as $key => $date) {
    		$manday = new Manday;
    		$manday->date = $date;
    		$manday->id = $manday->get_uid();
        array_push($mandays, $manday);
    }

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
    								->where('year', $request->input('year'))->get();
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

		$mansked->load('manskeddays');
		$mandays = $mansked->manskeddays; 

    foreach ($mandays as $manday) {
    	$manday->load('manskeddtls');
    }



    $new_mansked = new Mansked;
		//return $mansked->getRefno();
		$new_mansked->refno 		= $new_mansked->getRefno();
		$new_mansked->date 			= $mansked->date->format('Y-m-d');
		$new_mansked->weekno		= $request->input('nweekno');
		$new_mansked->year			= $request->input('year');
		$new_mansked->branchid 	= $mansked->branchid;
		$new_mansked->managerid = $mansked->managerid;
		$new_mansked->mancost 	= $mansked->mancost;
		$new_mansked->notes 		= $mansked->notes;
		$new_mansked->id 				= $mansked->get_uid();

	


		\DB::beginTransaction(); //Start transaction!

		$new_mandays = [];
    foreach ($new_mansked->getDaysByWeekNo($request->input('nweekno'), $request->input('year')) as $key => $date) {
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
        		$new_manday->manskeddtls()->saveMany($new_mandtls);
        } catch(\Exception $e) {
          \DB::rollback();
          throw $e;
        }

        array_push($new_mandays, $new_manday);
    }

    try {
       	$new_mansked->save();
        try {
           	$new_mansked->manskeddays()->saveMany($new_mandays);
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









}


