<?php

namespace App\Http\Controllers;

use Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Branch;
use App\Repositories\EmployeeRepository;
use App\Repositories\Criterias\ByBranchCriteria as ByBranch;
use App\Repositories\Criterias\ActiveEmployeeCriteria as ActiveEmployee;


class EmployeeController extends Controller {

	protected $employees;
	protected $branches;

	public function __construct(Request $request, EmployeeRepository $employeesrepo) {
		$this->employees = $employeesrepo;
		$this->employees->pushCriteria(new ByBranch($request));
		$this->employees->pushCriteria(new ActiveEmployee);
		//$this->branches = Branch::orderBy('code')->get();
	}


	public function getIndex(Request $request, $brcode, $param1=null, $param2=null, $param3=null) {

		


		if(strtolower($param1)==='add'){
			return $this->makeAddView($request);
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit') {
			return $this->makeEditView($request, $param1);
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1)) {   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeSingleView($request, $param1);
		} else if(strtolower($param1)==='list' && is_null($param2)) {
			return $this->makeListView($request, $param1, $param2);
		} else {
			return $this->makeDashboard($request);
		}
	}

	public function dt() {
		return Datatables::of($this->employees->all())->make(true);
		return Datatables::of(Employee::with(['position', 'branch'])->select('*'))->make(true);
	}


	public function makeDashboard(Request $request) {
		$data = [];
		$data['positions']['datas'] = [];
		$data['positions']['total'] = 0;

		$e = $this->employees
		//->skipCache()
		->with(['position' => function($query){
			$query->select('code', 'descriptor', 'id');
		}, 'department' => function($query){
			$query->select('code', 'descriptor', 'id');
		}])->all(['code', 'firstname', 'lastname', 'middlename', 'positionid', 'deptid']);
		
		foreach ($e as $key => $employee) {
			$p = strtolower($employee->position->code);
			if(array_key_exists($p, $data['positions']['datas'])) {
				$data['positions']['datas'][$p]['count'] += 1;
			} else {
				$data['positions']['datas'][$p]['descriptor'] = $employee->position->descriptor;
				$data['positions']['datas'][$p]['count'] = 1;
			}
			$data['positions']['total'] += 1;
		}

		ksort($data['positions']['datas']);

		return view('employee.dashboard', compact('data'));
	}


	public function makeListView(Request $request, $table, $branchid) {

		//return view('employee.list');

		$employees = $this->employees
			->with([
				// 'branch' => function($query) {
				// 	$query->select('code', 'descriptor', 'id');
				// }, 
				'position' => function($query){
					$query->select('code', 'descriptor', 'id');
				}, 'department' => function($query){
					$query->select('code', 'descriptor', 'id');
				}
			])->paginate(10, ['code', 'lastname', 'firstname', 'id', 'branchid', 'positionid', 'middlename', 'deptid']);
		
		//return $employees;
		return view('masterfiles.employee.list', compact('employees'));
								//->with('employees', $employees);
		//return view('masterfiles.employee.list', ['employees' => $employees]); //same as top

	}

	public function makeAddView(Request $request) {
		$branches = Branch::orderBy('code')->get();
		return view('masterfiles.employee.add')
								->with('branches', $branches);
	}

	public function makeSingleView(Request $request, $id) {
		$employee = Employee::with(['branch' => function ($query) {
                                $query->select('code', 'descriptor', 'addr1', 'id');
                        }])->where('id', $id)
                        ->get()
                        ->first();
		return view('masterfiles.employee.view')
								->with('employee', $employee);
	}

	public function makeEditView(Request $request, $id) {
		//$branches = Branch::orderBy('code')->get();
		$employee = Employee::with(['branch' => function ($query) {
                                $query->select('code', 'descriptor', 'addr1', 'id');
                        }])->where('id', $id)
                        ->get()
                        ->first();
		return view('masterfiles.employee.edit')
								->with('employee', $employee)
								->with('branches', $this->branches);
	}



	public function search(Request $request, $param1=null) {

    $limit = empty($request->input('maxRows')) ? 10:$request->input('maxRows'); 
    $res = Employee::where('branchid', $request->user()->branchid)
    				->where(function ($query) use ($request) {
              $query->orWhere('code', 'like', '%'.$request->input('q').'%')
          			->orWhere('lastname', 'like',  '%'.$request->input('q').'%')
		            ->orWhere('firstname', 'like',  '%'.$request->input('q').'%')
		            ->orWhere('middlename',  'like', '%'.$request->input('q').'%');
            })
            ->take($limit)
            ->get();

		return $res;
	}


	public function getByField($field, $value){
		
		$employee = Employee::with('position')->where($field, '=', $value)->first();
		
		if($employee){
			$respone = array(
						'code'=>'200',
						'status'=>'success',
						'message'=>'Hello '. $employee->firstname. '=)',
						'data'=> $employee->toArray()
			);	
			
		} else {
			$respone = array(
						'code'=>'404',
						'status'=>'danger',
						'message'=>'Invalid RFID! Record no found.',
						'data'=> ''
			);	
		}
				
		return $respone;
	} 


	public function post(Request $request){
		dd($request->all());

	}

	public function put(Request $request){
		dd($request->all());

	}
}