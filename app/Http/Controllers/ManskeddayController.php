<?php namespace App\Http\Controllers;

use DB;
use URL;
use Auth;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use App\Models\Manskedhdr as Mansked;
use App\Models\Manskedday as Manday;
use App\Models\Manskeddtl as Mandtl;
use Illuminate\Http\Request;
use App\Repositories\EmployeeRepository as EmployeeRepo;
use App\Repositories\ManskeddayRepository as MandayRepo;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\Criterias\ActiveEmployeeCriteria as ActiveEmployee;
use App\Events\Employee\Resigned as EmployeeResigned;
use App\Events\Employee\ChangeStatus as EmployeeChangeStatus;

class ManskeddayController extends Controller {

	protected $branchid = '';
	private $employees;
	protected $manday;
	protected $ds;

	public function __construct(EmployeeRepo $employees, MandayRepo $manday, DSRepo $ds) {
		$this->ds = $ds;
		$this->employees = $employees;
		$this->employees->pushCriteria(new ActiveEmployee);
		$this->manday = $manday;
		$this->branchid = Auth::user()->branchid;
	}

	public function getIndex(Request $request, $param1=null, $param2=null){
		
		if(strtolower($param1)==='add'){
			return $this->makeAddView($request);
		} else if((strtolower($param1)==='week') && preg_match('/^[0-9]+$/', $param2)) {
			return $this->makeViewWeek();
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit') {
			return $this->makeEditView($request, $param1); //task/manday/{id}/edit
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1)) {   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeSingleView($request, $param1); //task/manday/{id}
		} else {
			return $this->makeListView($request, $param1, $param2);
		}
	}

	// for $this->empGrpByDeptWithManday // deprecated
	public function empGrpByDept() {
		$depts = [['name'=>'Dining', 'employees'=>[], 'deptid'=>['75B34178674011E596ECDA40B3C0AA12', '201E68D4674111E596ECDA40B3C0AA12', '20767330A25B11E583CA00FF59FBB323']],
					['name'=>'Kitchen', 'employees'=>[], 'deptid'=>['71B0A2D2674011E596ECDA40B3C0AA12']]];

		for($i=0; $i<= 1; $i++) { 
			$employees = Employee::with('position')
									->select('lastname', 'firstname', 'positionid', 'employee.id')
									->join('position', 'position.id', '=', 'employee.positionid')
									->where('branchid', $this->branchid)
									->whereIn('deptid', $depts[$i]['deptid'])
					       	//->orderBy('position.ordinal', 'ASC')
					       	->orderBy('employee.lastname', 'ASC')
					       	->orderBy('employee.firstname', 'ASC')
					       	->get();
			$depts[$i]['employees'] = $employees;

		}
		return  $depts;
	}

	// for $this->makeEditView and $this->makeSingleView
	private function old_empGrpByDeptWithManday(Request $request, $id){
		//$depts = $this->empGrpByDept(); // get array of dept w/ emp grouped by department e.g. dining, kitchen
			$depts = $this->employees->byDepartment($request);
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

	private function empGrpByDeptWithManday(Request $request, $id) {
		return $id;
	}


	
	private function byDeptFrmEmpIds($manday){ 

		$empsOnMansked = $manday->manskeddtls->pluck('employeeid');
		
		$depts = $this->employees->byDeptFrmEmpIds($empsOnMansked->toArray());

		$mandtls = $manday->manskeddtls;

		for($h=0; $h<count($depts); $h++){
				$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
				for($i=0; $i<count($arr); $i++){
					
					$mandtl = $mandtls
										->where('employeeid', $depts[$h]['employees'][$i]->id)
  									->where('mandayid', $manday->id)
  									->first();
					
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


	private function byDeptFrmEmpIdsCombined($manday, $depts){ 

		//$empsOnMansked = $manday->manskeddtls->pluck('employeeid');
		
		//$depts = $this->employees->byDeptFrmEmpIds($empsOnMansked->toArray());

		$mandtls = $manday->manskeddtls;

		for($h=0; $h<count($depts); $h++){
				$arr = $depts[$h]['employees']->toArray(); // extract emp on each dept
				for($i=0; $i<count($arr); $i++){
					
					$mandtl = $mandtls
										->where('employeeid', $depts[$h]['employees'][$i]->id)
  									->where('mandayid', $manday->id)
  									->first();
					
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

	// for $this->makeSingleView
	private function hourlyDuty($depts){
		//return $depts;

		$arr = [];
		$sorted = [];

		foreach($depts as $dept){
			for($i = 0; $i < count($dept['employees']); $i++){
      	if($dept['employees'][$i]['manskeddtl']['daytype'] == 1){

      		$ts = $dept['employees'][$i]['manskeddtl']['timestart'];
      		$bs = $dept['employees'][$i]['manskeddtl']['breakstart'];
      		$be = $dept['employees'][$i]['manskeddtl']['breakend'];
      		$te = $dept['employees'][$i]['manskeddtl']['timeend'];

      		if($ts!='off' && $bs!='off' && $be!='off' && $te!='off'){
      			$hrs = $this->getHour($ts, $te);
	      		foreach ($hrs as $hr) {
	      			if(array_key_exists('hr_'.$hr, $arr)) {
	      				$arr['hr_'.$hr] += 0;
							} else {
								$arr['hr_'.$hr] = 0;
							}
	      		}
      		}

      		if($ts!='off' && $bs!='off'){
      			$this->consoHours($ts, $bs, $arr);
      		}
      		
      		if($be!='off' && $te!='off'){
      			$this->consoHours($be, $te, $arr);
	      	}

	      	if($ts!='off' && $te!='off' && $bs=='off' && $be=='off'){
	      		$this->consoHours($ts, $te, $arr);
	      	}
      	
      	}
      }
    } 
   
    foreach($arr as $key => $value){ 
      $x = explode('_', $key);
      $sorted[$x[1]] = $value;
    }
    ksort($sorted);

    return $sorted;
	}

	// for $this->hourlyDuty
	private function consoHours($s, $e, &$arr){
		$hrs = $this->getHour($s, $e);
		foreach ($hrs as $hr) {
			if(array_key_exists('hr_'.$hr, $arr)) {
				$arr['hr_'.$hr] += 1;
			} else {
				$arr['hr_'.$hr] = 1;
			}
		}
	}

	// for $this->consoHours
	private function getHour($start, $end){
		$arr = [];
		if($start!='off' || $start!='0.00' || !empty($start) || $end!='0.00' || !empty($end)){
			$s = explode(':', $start);
			$e = explode(':', $end);

			$e = $e[0] < $s[0] ? $e[0]+24 : $e[0];
			$s = $s[0];

			for($i = $s; $i < $e; $i++) {
				$arr[] = intval($i);
			}
		}
		return $arr;
	}


	public function makeEditView(Request $request, $param1) {
		//$manday = Manday::with('manskedhdr')->find($param1);
		//return hourly();
		//foreach (config('gi-dtr.hours') as $key => $value)
		//	echo date('g:i A', strtotime($value.':00')).'<br>';

		$manday = $this->manday
			->skipCache()
			->with('manskeddtls')
			->find($param1);

		

		//return $manday;
		if(count($manday) > 0){ // check if the $id 
			//if ($request->has('edit')) {
			if ((strtotime($manday->date) < strtotime('now')) && (!$request->has('edit') && $request->input('edit')!='true')) {
				return redirect(URL::previous())->with(['alert-warning' => 'Editing is disabled! Date already passed...']);
			}

			$empsOnMansked = $manday->manskeddtls->pluck('employeeid')->toArray(); // array of employeeids on mansked
			$currentEmpsOnBranch = $this->employees->all(['id'])->pluck('id')->toArray(); // array of employeeids on branch
			$combined_empids = collect($empsOnMansked)->merge($currentEmpsOnBranch)->unique()->values()->all();

			$e = $this->employees->byDeptFrmEmpIds($combined_empids);

			$depts = $this->byDeptFrmEmpIdsCombined($manday, $e);

			//return $depts = $this->empGrpByDeptWithManday($request, $param1);			
		} else {
			return redirect('task/mansked');
		}
		//return date('H:i', strtotime('08:00'));
		//return view('task.manday.edit')->with('depts', $depts)->with('manday', $manday);
		return view('task.manday.edit2')->with('depts', $depts)
																		->with('manday', $manday)
																		->with('hours', $this->hourlyDuty($depts));

	}

	// deprecated
	public function makeAddView(Request $request) {

		$depts = $this->empGrpByDept();
		//return $depts = $this->employees->byDepartment($request);

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
		//$manday = Manday::find($param1);
		$manday = $this->manday
			->skipCache()
			->with('manskeddtls')
			->find($param1);

		$edit = count($manday->manskeddtls);

		//return dd($request);
		if(count($manday) > 0) { // check if the $id 
			$depts = $this->byDeptFrmEmpIds($manday, true);
		} else {
			return redirect(URL::previous());
		}
		//return $this->hourlyDuty($depts);
		//return $depts;
		return view('task.manday.view')
							->with('depts', $depts)
							->with('edit', $edit)
							->with('manday', $manday->load('manskedhdr'))
							->with('hours', $this->hourlyDuty($depts));
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
		//return $request->input('manskeddtls');
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
		          foreach ($request->input('manskeddtls') as $mandtl) {
								
								if ($mandtl['daytype']=='5' || $mandtl['daytype']=='7' || $mandtl['daytype']=='8') {
									
									$e = Employee::find($mandtl['employeeid']);
									$e->empstatus = 4;
									$e->datestop = $manday->date->format('Y-m-d');

									switch ($mandtl['daytype']) {
										case '7':
											$status = 'awol';
											break;
										case '8':
											$status = 'did not show up';
											break;
										default:
											$status = 'resigned';
											break;
									}
									
									if ($e->save()) {
										if (app()->environment()==='production')
                			event(new EmployeeChangeStatus($e, $status));
									}

								} else {

									$n = Mandtl::find($mandtl['id']);
									if(count($n) > 0){
										foreach ($mandtl as $key => $value) {
											if(isset($mandtl['timestart']) && $mandtl['timestart']=='off' && $key=='timestart'){
												$n->breakstart = NULL;
												$n->breakend = NULL;
												$n->timeend = NULL;
												$n->{$key} = NULL;
											} else {
												$n->{$key} = $value;
											}
										}
										$n->save();
									} else {
										$m = new Mandtl;
										foreach ($mandtl as $key => $value) {
											if($key=='id')
												$m->id = $m->get_uid();
											else
												if ($mandtl['timestart']=='off' && $key=='timestart')
													$m->{$key} = NULL;
												else 
													$m->{$key} = $value;
										}
										$m->mandayid = $request->input('id');
										$m->save();
										//\DB::rollback();
										//return 'no mandtl found!';
									}
								}
							}

							$target_mancostpct = ($manday->custcount*$manday->headspend) == 0
								? 0
								: (($manday->empcount*$manday->manskedhdr->mancost)/($manday->custcount*$manday->headspend)*100);

							$attrs = [
								'branchid' 		=> $request->user()->branchid,
								'date'				=> $manday->date->format('Y-m-d'),
								'managerid'		=> $request->user()->id,
								'target_cust'	=> $manday->custcount,
								'target_headspend' 	=> $manday->headspend,
								'target_empcount' 	=> $manday->empcount,
								'target_mancostpct' => number_format($target_mancostpct,2)
							];

							$this->ds->firstOrNew($attrs, ['branchid', 'date']);

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


