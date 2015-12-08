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

Route::group(['middleware' => 'auth'], function(){

Route::get('task/mansked/{param1?}/{param2?}/{param3?}', ['uses'=>'ManskedController@getIndex'])
	->where(['param1'=>'add|[0-9]{4}+', 
					'param2'=>'week|[0-9]+', 
					'param3'=>'edit|[0-9]+|[A-Fa-f0-9]{32}+']);

Route::get('task/manday/{param1?}/{param2?}/{param3?}', ['uses'=>'ManskeddayController@getIndex'])
	->where(['param1'=>'add|[A-Fa-f0-9]{32}+', 
					'param2'=>'edit|branch|[0-9]+', 
					'param3'=>'edit|[A-Fa-f0-9]{32}+']);

Route::get('dtr/{param1?}/{param2?}/{param3?}/{param4?}', ['uses'=>'DtrController@getIndex'])
->where(['param1'=>'generate|[0-9]{4}+', 
				'param2'=>'[0-9]{02}+', 
				'param3'=>'edit|[A-Fa-f0-9]{32}|[0-9]{02}+',
				'param4'=>'[A-Fa-f0-9]{32}+']);


Route::get('dtr/generate', ['uses'=>'DtrController@index']);
Route::get('reports/dtr/{date}', ['uses'=>'DtrController@getDtrReports']);
Route::post('dtr/generate', ['uses'=>'DtrController@postGenerate']);


Route::get('backups/web/{param1?}/{param2?}', ['uses'=>'UploadController@indexWeb']);
Route::get('backups/pos/{param1?}/{param2?}', ['uses'=>'UploadController@indexPos']);
Route::get('backups/files/{param1?}/{param2?}', ['uses'=>'UploadController@indexFiles']);
Route::get('backups/upload', ['uses'=>'UploadController@getBackupUpload']);
Route::get('backups', ['uses'=>'UploadController@index']);

Route::post('upload/postfile', ['as'=>'upload.postfile', 'uses'=>'UploadController@postfile']); // upload to web
Route::put('upload/postfile', ['as'=>'upload.putfile', 'uses'=>'UploadController@putfile']); // move from web to storage


/******************* API  *************************************************/
Route::group(['prefix'=>'api'], function(){

Route::post('t/employee', ['as'=>'employee.post', 'uses'=>'EmployeeController@post']);
Route::put('t/employee', ['as'=>'employee.put', 'uses'=>'EmployeeController@put']);




Route::post('t/mansked', ['as'=>'mansked.post', 'uses'=>'ManskedController@post']);	
Route::post('c/mansked', ['as'=>'mansked.copy', 'uses'=>'ManskedController@copyMansked']);	

Route::post('t/manskedday', ['as'=>'manday.post', 'uses'=>'ManskeddayController@post']);
Route::put('t/manskedday/{id}', ['as'=>'manday.put', 'uses'=>'ManskeddayController@put']);

});/******* end prefix:api ********/


}); /******* end middeware:auth ********/

// for TK
Route::post('api/timelog', ['as'=>'timelog.post', 'uses'=>'TimelogController@post']);
Route::get('tk', ['as'=>'tk.index','uses'=>'TimelogController@getIndex']);
Route::get('api/employee/{field?}/{value?}', ['as'=>'employee.getbyfield', 'uses'=>'EmployeeController@getByField']);








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











get('image/employee/{id}', function($id){
	
	$emp_photo = new App\Models\Empphoto;
	$emp_photo->setConnection('mysql-hr');
	$photo = $emp_photo->find($id);

	echo '<img src="data:img/jpg;base64, '.base64_encode($photo->image).'" />';
});


get('doc/employee/{id}', function($id){
	
	$emp_doc = new App\Models\Empdoc;
	$emp_doc->setConnection('mysql-hr');
	$doc = $emp_doc->find($id);

	//header("Content-Type: application/pdf");
	//echo $doc->image;

  $response = Response::make($doc->image, 200);
  $response->header('Content-Type', 'application/pdf');
  //$response->header('Content-Disposition', 'attachment; filename="downloaded.pdf"');

  return $response;

});




get('dtr-repo/{date}', ['uses'=>'DtrController@date']);
get('dtl-repo/{date}', ['uses'=>'DtrController@date']);

get('slug/branch/{id}', function($id){
	$branch = App\Models\Branch::find($id);
	return str_slug($branch->address);
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


get('manday-count', function(){
	
	//$manday = App\Models\Manskedday::with('countMandtls')->where('date', '2015-11-13')->first();
	$manday = App\Models\Manskedday::with('countMandtls')->where('date', '2015-11-13')->first();
	return $manday;

});


get('folder/{f}', function($f){
	$f='.';
	return '/' . trim(str_replace('..', '', $f), '/');
});

get('mandtl-scope/{employeeid}/{date}', function($employeeid, $date){
	$branch = App\Models\Manskeddtl::with('employee')->whereEmployeeid($employeeid)->date($date)->get();
	//$branch = App\Models\Manskeddtl::whereEmployeeid($employeeid)->first()->manskedday()->where('date', '2015-11-13')->first();
	return $branch;
});

get('timelogs/{employeeid}/{date}', function($employeeid, $date){
	//$timelogs = App\Models\Timelog::employeeid($employeeid)->date($date)->get();
	//$branch = App\Models\Manskeddtl::whereEmployeeid($employeeid)->first()->manskedday()->where('date', '2015-11-13')->first();
	$date = Carbon\Carbon::parse($date);
	$timelogs = App\Models\Timelog::employeeid($employeeid)
									//->whereBetween(\DB::raw('DATE(datetime)'), 

										->whereBetween('datetime',[
											$date, 
											$date->copy()->addDay()->format('Y-m-d'). ' 06:00:00'
										])
                  //->date($date)
                  //->txncode(1)
                  ->orderBy('txncode', 'ASC')
                  ->orderBy('datetime', 'ASC')
                  ->get();

	return $timelogs;



});

get('holidate/{date}', function($date){
	
	$h = App\Models\Holidate::with('holiday.holidaydtls')->date($date)->first();
	return $h;
});


get('files', function(){
	//return dd(Storage::disk(app()->environment()));
	$directories = Storage::allDirectories(session('user.branchcode'));
	return $directories;
});




get('week/{weekno}', function($weekno){
	
	echo $weekno.'<br>';
	$week_start = new DateTime();
	$week_start->setISODate('2015',$weekno);
	echo $week_start->format('Y-m-d');
});



get('last-day-yr/{year}', function($year){
	
	$dt = Carbon\Carbon::parse($year.'-12-31');
	echo $dt->format('Y-m-d').'<br>';
	echo $dt->toRfc822String().'<br>';
	echo $dt->weekOfYear.'<br>';
});

get('week', function(){
	
	$dt = Carbon\Carbon::now();
	for ($i=0; $i<7 ; $i++) { 
		$dt->addDay();
	}
	return $dt->weekOfYear;
});


get('email', function(){
		$data = [];
	 return Mail::send('emails.welcome', $data, function ($message) {
	 	$message->subject('Test Email');
    $message->from('no-reply@giligansrestaurant.com', 'Giligan\'s');

    $message->to('freakyash_02@yahoo.com');
	});
});


get('phpinfoko', function(){
	

	return phpinfo();
});









 

