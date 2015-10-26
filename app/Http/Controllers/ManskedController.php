<?php

namespace App\Http\Controllers;

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



	public function getIndex(Request $request, $param1=null, $param2=null){
		
		if(strtolower($param1)==='add'){
			return $this->makeAddView($request);
		} else if((strtolower($param1)==='week') && preg_match('/^[0-9]+$/', $param2)) {
			return $this->makeViewWeek($param2);
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit') {
			return $this->makeEditView($request, $param1);
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1)) {   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeSingleView($request, $param1);
		} else {
			return $this->makeListView($request, $param1, $param2);
		}
		
	}



	public function makeAddView(Request $request) {
		$lastday = Mansked::getLastDayLastWeekOfYear();
		$branch = Branch::find(Auth::user()->branchid);
		$data = [
			'branch' => $branch->code.' - ' .$branch->addr1,
			'branchid' => $branch->id,
			'manager' => Auth::user()->name,
			'managerid' => Auth::user()->id
			];
		return view('task.mansked.add')->with('lastday', $lastday)->with('data', $data);
	}

	public function makeListView(Request $request, $param1, $param2) {
		//return dd(app());
		$manskeds = Mansked::with('manskeddays')
													->where('branchid', $this->branchid)
													->orderBy('weekno', 'DESC')->paginate('5');
		if(count($manskeds) <= 0){
			$manskeds = new Mansked;
			$new = $manskeds->newWeek($this->branchid);
		} else {
			$new = $manskeds[0]->newWeek($this->branchid);
		}
		
		
		//return count($manskeds);
		//return $manskeds->newWeek($this->branchid);
		return view('task.mansked.list2')->with('manskeds', $manskeds)->with('new', $new);

		//$weeks = Mansked::paginateWeeks($request, '2015', 5);
		//return view('task.mansked.list')->with('weeks', $weeks);
	}


	public function makeViewWeek($weekno){

		$depts = $this->empGrpByDept();


		$mansked = Mansked::with('manskeddays')
  												->where('weekno', $weekno)
  												->where('branchid', Auth::user()->branchid)
  												->get()->first();

  	

  	$days = $mansked->manskeddays;

		//return $days[0]->date;

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

		//

		 $this->validate($request, [
        'date' => 'required|date|max:10',
        'weekno' => 'required',
    ]);

		 // check weekno if exist
		$mansked = Mansked::whereWeekno($request->input('weekno'))->get();
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

		return $mansked;

				
	}









}


