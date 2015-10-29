<?php

Route::get('/', ['middleware' => 'auth', function () {

	return view('index');

	return $emp = App\Models\Employee::with(['branch'=>function($query){
		$query->select('addr1', 'id');
	}])->where('id', Auth::user()->id)->get(['firstname', 'lastname', 'branchid'])->first();
  
  return $emp->branch->addr1;
}]);

Route::get('dashboard', ['middleware' => 'auth', function () {
    return view('index');
}]);





Route::get('login', ['as'=>'auth.getlogin', 'uses'=>'Auth\AuthController@getLogin']);
Route::post('login', ['as'=>'auth.postlogin', 'uses'=>'Auth\AuthController@postLogin']);
Route::get('logout', ['as'=>'auth.getlogout', 'uses'=>'Auth\AuthController@getLogout']);


Route::get('task/mansked/{param1?}/{param2?}/{param3?}', ['uses'=>'ManskedController@getIndex',  'middleware' => 'auth'])
	->where(['param1'=>'add|[0-9]{4}+', 
					'param2'=>'week|[0-9]+', 
					'param3'=>'edit|[0-9]+|[A-Fa-f0-9]{32}+']);

Route::get('task/manday/{param1?}/{param2?}/{param3?}', ['uses'=>'ManskeddayController@getIndex',  'middleware' => 'auth'])
	->where(['param1'=>'add|[A-Fa-f0-9]{32}+', 
					'param2'=>'edit|branch|[0-9]+', 
					'param3'=>'edit|[A-Fa-f0-9]{32}+']);

	/******************* API  *************************************************/
Route::group(['prefix'=>'api'], function(){

Route::post('t/employee', ['as'=>'employee.post', 'uses'=>'EmployeeController@post']);
Route::put('t/employee', ['as'=>'employee.put', 'uses'=>'EmployeeController@put']);
Route::get('employee/{field?}/{value?}', ['as'=>'employee.getbyfield', 'uses'=>'EmployeeController@getByField']);
Route::post('timelog', ['as'=>'timelog.post', 'uses'=>'TimelogController@post']);


Route::post('t/mansked', ['as'=>'mansked.post', 'uses'=>'ManskedController@post']);	
Route::post('c/mansked', ['as'=>'mansked.copy', 'uses'=>'ManskedController@copyMansked']);	

Route::post('t/manskedday', ['as'=>'manday.post', 'uses'=>'ManskeddayController@post']);
Route::put('t/manskedday/{id}', ['as'=>'manday.put', 'uses'=>'ManskeddayController@put']);

});



get('csv/{year}/week/{weekno}', function($year, $weekno){
	$manskeds = App\Models\Manskedhdr::with('manskeddays')->where('weekno', $weekno)->get()->first();

	echo 'Date,Customers,Head Spend,Crew';
  echo PHP_EOL;
	foreach ($manskeds->manskeddays as $manday) {
		echo $manday->date.',';
		echo empty($manday->custcount) ? 0: $manday->custcount;
		echo ',';
		echo empty($manday->headspend) ? 0: $manday->headspend;
		echo ',';
		echo empty($manday->empcount) ? 0: $manday->empcount;
		echo PHP_EOL;
	}

});









get('const', function(){
	return view('index');
});

get('sessions', function(){
	return session()->all();
});

get('flush-sessions', function(){
	Session::flush();
	return redirect('sessions');
});



get('week/{weekno}', function($weekno){
	
	echo $weekno.'<br>';
	$week_start = new DateTime();
	$week_start->setISODate('2015',$weekno);
	echo $week_start->format('Y-m-d');
});



get('last-day-yr/{year}', function($year){
	
	$dt = Carbon\Carbon::parse($year.'-12-31');
	return $dt->weekOfYear;
});

