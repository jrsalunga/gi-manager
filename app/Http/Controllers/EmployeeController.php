<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Branch;

class EmployeeController extends Controller {

	protected $branches;

	public function __construct() {
		$this->branches = Branch::orderBy('code')->get();
	}


	public function getIndex(Request $request, $menu, $table, $param1, $param2) {
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


	public function makeListView(Request $request, $table, $branchid) {
		
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


	public function getByField($field, $value){
		
		$employee = Employee::where($field, '=', $value)->first();
		
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