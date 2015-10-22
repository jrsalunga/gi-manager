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
use URL;
use Carbon\Carbon;

class ManskeddayController extends Controller {

	protected $branchid = '';

	public function __construct(){
		$this->branchid = Auth::user()->branchid;
	}

	public function getIndex(Request $request, $param1=null, $param2=null){
		
		if(strtolower($param1)==='add'){
			return $this->makeAddView($request);
		} else if((strtolower($param1)==='week') && preg_match('/^[0-9]+$/', $param2)) {
			return $this->makeViewWeek();
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit') {
			return $this->makeEditView($request, $param1);
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1)) {   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeSingleView($request, $param1);
		} else {
			return $this->makeListView($request, $param1, $param2);
		}
	}



	private function empGrpByDept() {
		$depts = [['name'=>'Dining', 'employees'=>[], 'deptid'=>['75B34178674011E596ECDA40B3C0AA12', '201E68D4674111E596ECDA40B3C0AA12']],
					['name'=>'Kitchen', 'employees'=>[], 'deptid'=>['71B0A2D2674011E596ECDA40B3C0AA12']]];

		for($i=0; $i<= 1; $i++) { 
			$employees = Employee::with('position')
									->select('lastname', 'firstname', 'positionid', 'employee.id')
									->join('position', 'position.id', '=', 'employee.positionid')
									->where('branchid', $this->branchid)
									->whereIn('deptid', $depts[$i]['deptid'])
					       	->orderBy('position.ordinal', 'ASC')
					       	->orderBy('employee.lastname', 'ASC')
					       	->orderBy('employee.firstname', 'ASC')
					       	->get();
			$depts[$i]['employees'] = $employees;

		}
		 return  $depts;
	}

	private function empGrpByDeptWithManday($id){
		$depts = $this->empGrpByDept(); // get array of dept w/ emp grouped by department e.g. dining, kitchen
			for($h=0; $h<count($depts); $h++){
				$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
				for($i=0; $i<count($arr); $i++){
					$mandtl = Mandtl::where('employeeid', $depts[$h]['employees'][$i]->id)
													->where('mandayid', $id)->get()->first();
					$depts[$h]['employees'][$i]['manskeddtl'] = count($mandtl) > 0 ?
						['daytype'=> $mandtl->daytype, 
						'timestart'=>$mandtl->timestart,
						'breakstart'=>$mandtl->breakstart,
						'breakend'=>$mandtl->breakend,
						'timeend'=>$mandtl->timeend,
						'workhrs'=>$mandtl->workhrs,
						'breakhrs'=>$mandtl->breakhrs,
						'loading'=>$mandtl->loading, 
						'id'=>$mandtl->id]: 
						['daytype'=> 0, 
						'timestart'=>'off',
						'breakstart'=>'',
						'breakend'=>'',
						'timeend'=>'',
						'workhrs'=>'',
						'breakhrs'=>'',
						'loading'=>'', 
						'id'=>''];
				}
			}
		return $depts;
	}







	public function makeEditView(Request $request, $param1) {
		$manday = Manday::find($param1);
		if(strtotime($manday->date) < strtotime('now')){
			return redirect(URL::previous())->with(['alert-warning' => 'Editing is disabled! Date already passed...']);
		}
		if(count($manday) > 0){ // check if the $id 
			$depts = $this->empGrpByDeptWithManday($param1);			
		} else {
			return redirect(URL::previous());
		}
		//return $depts;
		//return view('task.manday.edit')->with('depts', $depts)->with('manday', $manday);
		return view('task.manday.edit2')->with('depts', $depts)->with('manday', $manday);
	}

	public function makeAddView(Request $request) {

		$depts = $this->empGrpByDept();

		$date = (!empty($request->input('date')) && strtotime('now') < strtotime($request->input('date')) ) ? $request->input('date'):date('Y-m-d', strtotime('now +1day'));
		//exit;
		
		//return $employees;
		return view('task.manday.add')->with('depts', $depts)->with('date', $date);
	}

	public function makeListView(Request $request, $param1, $param2) {
		$weeks = Mansked::paginateWeeks($request, '2015', 5);
		//return $weeks;
		return view('task.mansked.list')->with('weeks', $weeks);
	}

	//task/manday/{id}
	public function makeSingleView(Request $request, $param1){
		$manday = Manday::find($param1);
		//return $manday;
		if(count($manday) > 0){ // check if the $id 
			$depts = $this->empGrpByDeptWithManday($param1);	
			session(['weekno'=>Carbon::parse($manday->date)->weekOfYear])	;	
		} else {
			return redirect(URL::previous());
		}
		return view('task.manday.view')->with('depts', $depts)
																	->with('manday', $manday);
	}


	public function makeViewWeek(){
		return view('task.mansked.week');
	}


	public function testWeeks(Request $request) {
		$weeks = Mansked::paginateWeeks($request, '2015');
		return view('task.mansked.list')->with('weeks', $weeks);
	}








	public function post(Request $request){
		return $request->all();
	}

	public function put(Request $request, $id){
		//return $request->all();
		if(strtolower($request->input('id')) == strtolower($id)){
			$manday = Manday::find($id);
			if(count($manday) > 0){
				//\DB::beginTransaction();
				$manday->custcount 	= $request->input('custcount');
				$manday->headspend 	= $request->input('headspend');
				$manday->empcount 	= $request->input('empcount');
				$manday->workhrs 		= $request->input('workhrs');
				$manday->breakhrs 	= $request->input('breakhrs');
				$manday->overload 	= $request->input('overload');
				$manday->underload 	= $request->input('underload');

				\DB::beginTransaction(); //Start transaction!
		    try {
		      $manday->save();
		        try {
		          foreach($request->input('manskeddtls') as $mandtl){
								$n = Mandtl::find($mandtl['id']);
								if(count($n) > 0){
									//dd(count($n));
									foreach ($mandtl as $key => $value) {
										if($mandtl['timestart']=='off'){
											$n->breakstart = 'off';
											$n->breakend = 'off';
											$n->timeend = 'off';
										}
										$n->{$key} = $value;
									}
									$n->save();
								} else {
									//dd($mandtl);
									$m = new Mandtl;
									foreach ($mandtl as $key => $value) {
										if($key=='id')
											$m->id = $m->get_uid();
										else
											$m->{$key} = $value;
									}
									$m->mandayid = $request->input('id');
									$m->save();
									//\DB::rollback();
									//return 'no mandtl found!';
								}
							}
		        } catch(\Exception $e) {
		          \DB::rollback();
		          throw $e;
		        }
		    } catch(\Exception $e) {
		      \DB::rollback();
		      throw $e;
		    }
		    \DB::commit();
				
				//$manday->load('manskeddtls');
				//return $manday;
				//return $request->input('manskeddtls');
			}
		}
		return redirect('/task/manday/'.$manday->lid())->with('alert-success', 'Record saved!');
		
		//return ['iid' => $request->input('id'),  'rid'=>$id];
	}











}


