<?php

namespace App\Http\Controllers;

use Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Branch;
use App\Repositories\EmployeeRepository;
use App\Repositories\Filters\ByBranch;

class EmployeeController extends Controller {

	protected $employees;
	protected $branches;

	public function __construct(Request $request, EmployeeRepository $employeesrepo) {
		$this->employees = $employeesrepo;
		//$this->employees->pushFilters(new ByBranch($request));
		$this->branches = Branch::orderBy('code')->get();
	}


	public function getIndex(Request $request, $param1=null, $param2=null, $param3=null) {


		if(strtolower($param1)==='add'){
			return $this->makeAddView($request);
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1) && strtolower($param2)==='edit') {
			return $this->makeEditView($request, $param1);
		} else if(preg_match('/^[A-Fa-f0-9]{32}+$/', $param1)) {   //preg_match('/^[A-Fa-f0-9]{32}+$/',$action))
			return $this->makeSingleView($request, $param1);
		} else {
			return $this->makeListView($request, $param1, $param2);
		}
	}

	public function dt() {
		return Datatables::of($this->employees->all())->make(true);
		return Datatables::of(Employee::with(['position', 'branch'])->select('*'))->make(true);
	}


	public function makeListView(Request $request, $table, $branchid) {
		//return dd($this->employees->paginate(10));
		return view('employee.list');
		$employees = Employee::with(['branch' => function($query){
													$query->select('code', 'descriptor', 'id');
												}, 'position' => function($query){
													$query->select('code', 'descriptor', 'id');
												}])
												//->select('code', 'branchid')
												->paginate(10);
		if(!empty($table) && !empty($branchid)){
			//$employees
		}

		//return $employees;
		return view('masterfiles.employee.list')
								->with('employees', $employees);
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