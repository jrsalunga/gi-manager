<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Timelog;
use Validator;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Http\Response;



class TimelogController extends Controller {

	private $_branchid;

	public function __construct(Request $request){
		if(is_null(session('user.branchid')) && is_null($request->cookie('branchid')))
			return redirect()->route('auth.getlogin');
		else
			$this->_branchid = is_null(session('user.branchid')) ? $request->cookie('branchid') : session('user.branchid');
	}


	public function getIndex(Request $request) {
		if($_SERVER['HTTP_X_FORWARDED_FOR']==='127.0.0.1'){
			
			$timelogs = Timelog::with('employee.branch')
											->orderBy('datetime', 'DESC')
											->take(20)
											->get();
		} else {

			$timelogs = Timelog::with(['employee'=>function($query){
													$query->with([
															'branch'=>function($query){
																$query->select('code', 'descriptor', 'id');
															}, 
															'position'=>function($query){
																$query->select('code', 'descriptor', 'id');
															}])->select('code', 'lastname', 'firstname', 'branchid', 'positionid', 'id');
														
												}])
											->select('timelog.*')
											->join('employee', function($join){
                            $join->on('timelog.employeeid', '=', 'employee.id')
                                ->where('employee.branchid', '=', $this->_branchid);
                            })
											->orderBy('datetime', 'DESC')
											->take(20)
											->get();
		}

		//return $timelogs;
		//if(count($timelogs) <= 0)
		//	return redirect()->route('auth.getlogin');
		
		$response = new Response(view('tk.index', compact('timelogs')));//->with('timelogs', $timelogs));
		$response->withCookie(cookie('branchid', $this->_branchid, 45000));
		return $response;

    //return view('tk.index', compact($timelogs));//->with('timelogs', $timelogs);		
	}




	public function post(Request $request){
		

		$rules = array(
			//'employeeid'	=> 'required',
			'datetime'      => 'required',
			'txncode'      	=> 'required',
			'entrytype'     => 'required',
			//'terminalid'    => 'required',
		);
		
		$validator = Validator::make($request->all(), $rules);


		if($validator->fails()) {
			
			$respone = array(
					'code'=>'400',
					'status'=>'error',
					'message'=>'Error on validation',
					//'data'=> $validator
			);
		} else {
			$employee = Employee::with('branch', 'position')->where('rfid', '=', $request->input('rfid'))->first();
			
			
			if(!isset($employee)){ // employee does not exist having the RFID submitted
				$respone = array(
						'code'=>'401',
						'status'=>'error',
						'message'=>'Invalid RFID: '.  $request->input('rfid'),
						'data'=> ''
				);	
			} else {
			
				$timelog = new Timelog;
				//$timelog->employeeid	= $request->get('employeeid');
				$timelog->employeeid  = $employee->id;
				$timelog->datetime 		= $request->input('datetime');
				$timelog->txncode 	 	= (strtolower($employee->branchid) == strtolower($this->_branchid)) ? $request->input('txncode'):'9';
				$timelog->entrytype  	= $request->input('entrytype');
				$timelog->rfid				= $employee->rfid;
				$timelog->terminalid 	= $request->cookie('branchcode')!==null ? $request->cookie('branchcode'):$_SERVER["REMOTE_ADDR"];
				//$timelog->terminal 	= gethostname();
				$timelog->id 	 	 			= strtoupper(Timelog::get_uid());
				
				if($timelog->save()){

					$respone = array(
						'code'=>'200',
						'status'=>'success',
						'message'=>'Record saved!',
					);	

					$datetime = explode(' ',$timelog->datetime);
				
					$data = array(
						'empno'			=> $employee->code,
						'lastname'	=> $employee->lastname,
						'firstname'	=> $employee->firstname,
						'middlename'=> $employee->middlename,
						
						'position'	=> $employee->position->descriptor,
						'date'			=> $datetime[0] ,
						'time'			=> $datetime[1] ,
						'txncode'		=> $timelog->txncode,
						'txnname'		=> $timelog->getTxnCode(),
						'branch' 		=> $employee->branch->code,
						'timelogid' => $timelog->id,
					);
				
					$respone['data'] = $data;

				} else {
					$respone = array(
						'code'=>'400',
						'status'=>'error',
						'message'=>'Error on saving locally!',
					);	
				}				
			}
		}
		return json_encode($respone);
	}
}