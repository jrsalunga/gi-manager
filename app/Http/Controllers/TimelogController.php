<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Timelog;
use Validator;
use Illuminate\Http\Request;
use App\Models\Employee;



class TimelogController extends Controller {


	public function getIndex() {
		
		$timelogs = Timelog::with('employee.branch')
											->orderBy('datetime', 'DESC')
											->get();
    return view('tk.index')->with('timelogs', $timelogs);		
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
			$employee = Employee::with('branch')->where('rfid', '=', $request->input('rfid'))->get();
			
			
			if(!isset($employee[0])){ // employee does not exist having the RFID submitted
				$respone = array(
						'code'=>'401',
						'status'=>'error',
						'message'=>'Invalid RFID: '.  $request->input('rfid'),
						'data'=> ''
				);	
			} else {
			
				$timelog = new Timelog;
				//$timelog->employeeid	= $request->get('employeeid');
				$timelog->employeeid    = $employee[0]->id;
				$timelog->datetime 		= $request->input('datetime');
				$timelog->txncode 	 	= $request->input('txncode');
				$timelog->entrytype  	= $request->input('entrytype');
				//$timelog->terminalid 	= $request->get('terminalid');
				$timelog->terminal 	= gethostname();
				$timelog->id 	 	 	= strtoupper(Timelog::get_uid());
				
				if($timelog->save()){

					$respone = array(
						'code'=>'200',
						'status'=>'success',
						'message'=>'Record saved!',
					);	



					$datetime = explode(' ',$timelog->datetime);
					$txncode = $timelog->txncode=='to' ? 'Time Out':'Time In';
				
					$data = array(
						'empno'		=> $employee[0]->code,
						'lastname'	=> $employee[0]->lastname,
						'firstname'	=> $employee[0]->firstname,
						'middlename'=> $employee[0]->middlename,
						'position'	=> $employee[0]->position ,
						'date'		=> $datetime[0] ,
						'time'		=> $datetime[1] ,
						'txncode'	=> $timelog->txncode,
						'txnname'	=> $txncode,
						'branch' => $employee[0]->branch->code,
						'timelogid' => $timelog->id
						
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