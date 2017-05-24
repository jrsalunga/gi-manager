<?php namespace App\Http\Controllers;

use StdClass;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\TimelogRepository as Timelog;

class TimesheetController extends Controller 
{ 
	public $timelog;

	public function __construct(DateRange $dr, Timelog $timelog) {
		$this->timelog = $timelog;
		$this->dr = $dr;
	}

	public function getRoute(Request $request, $brcode, $param1=null) {
		if(!is_null($param1) && $param1=='print')
			return $this->getPrintIndex($request);
		else if(!is_null($param1) && is_uuid($param1))
			return $this->getEmployeeDtr($request, $param1);
		else
			return $this->getIndex($request);
	}



	private function getIndex(Request $request){
		
		$date = is_null($request->input('date')) 
			? $this->dr->now 
			: carbonCheckorNow($request->input('date'));
		
		$data = $this->timelog->allByDate($date);
		//return dd($data);
		return $this->setViewWithDR(view('timesheet.index')
																	->with('dr', $this->dr)
																	->with('data', $data));
	}

	





	private function getEmployeeDtr(Request $request, $employeeid) {

		$employee = Employee::findOrFail($employeeid);

		foreach ($this->dr->dateInterval() as $key => $date) {
			
			$timesheets[$key]['date'] = $date;
			
			$timelogs = $this->timelog
			->skipCriteria()
			->getRawEmployeeTimelog($employeeid, $date, $date)
			->all();

			$timesheets[$key]['raw_timelog'] = $timelogs;
	
			//array_push($timesheets[$key]['timelog'], $this->timelog->generateTimesheet($employee->id, $date, collect($timelogs)));
			$timesheets[$key]['timelog'] = $this->timelog->generateTimesheet($employee->id, $date, collect($timelogs));
		}

		return $timesheets;

		$header = new StdClass;
		$header->totalWorkedHours = collect($timesheets)->pluck('timelog')->sum('workedHours');

		return 	$this->setViewWithDR(
							view('timesheet.employee-dtr')
							->with('timesheets', $timesheets)
							->with('employee', $employee)
							->with('header', $header)
							->with('dr', $this->dr)
						);
	}


	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
		return $response;
	}



}

