<?php
/*
Route::get('/', ['middleware' => 'auth', function () {

	$backup = App\Models\Backup::first();

	return $backup;


	$last_ds = Carbon\Carbon::parse('2016-04-01');
	$vfpdate = Carbon\Carbon::parse('2016-04-02');

	return dd($last_ds->lt($vfpdate));

	return view('index');

	return $emp = App\Models\Employee::with(['branch'=>function($query){
		$query->select('addr1', 'id');
	}])->where('id', Auth::user()->id)->get(['firstname', 'lastname', 'branchid'])->first();
  
  return $emp->branch->addr1;
}]);
*/





Route::get('login', ['as'=>'auth.getlogin', 'uses'=>'Auth\AuthController@getLogin']);
Route::post('login', ['as'=>'auth.postlogin', 'uses'=>'Auth\AuthController@postLogin']);
Route::get('logout', ['as'=>'auth.getlogout', 'uses'=>'Auth\AuthController@getLogout']);

Route::group(['middleware' => 'auth'], function(){

Route::get('/', ['uses'=>'DashboardController@getIndex']);
Route::get('/{brcode}/', ['uses'=>'DashboardController@getIndex']);
Route::get('dashboard', ['uses'=>'DashboardController@getIndex']);
Route::get('/{brcode}/dashboard', ['uses'=>'DashboardController@getIndex']);
//Route::get('analytics', ['uses'=>'DashboardController@getAnalytics']);
Route::get('analytics', ['uses'=>'AnalyticsController@getDaily']);
Route::get('analytics/month', ['uses'=>'AnalyticsController@getMonth']);
Route::get('analytics/week', ['uses'=>'AnalyticsController@getWeekly']);
Route::get('analytics/quarter', ['uses'=>'AnalyticsController@getQuarter']);
Route::get('analytics/year', ['uses'=>'AnalyticsController@getYear']);

Route::get('{brcode}/analytics', ['uses'=>'AnalyticsController@getDaily']);


Route::get('settings/{param1?}/{param2?}', ['uses'=>'SettingsController@getIndex'])
	->where(['param1'=>'password', 
					'param2'=>'week|[0-9]+']);

Route::post('/settings/password',  ['uses'=>'SettingsController@changePassword']);

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

Route::get('{brcode}/employee/{param1?}/{param2?}/{param3?}', ['uses'=>'EmployeeController@getIndex'])
->where(['param1'=>'list|add|[A-Fa-f0-9]{32}+', 
				'param2'=>'[0-9]{02}+', 
				'param3'=>'edit|[A-Fa-f0-9]{32}|[0-9]{02}+']);



Route::get('dtr/generate', ['uses'=>'DtrController@index']);
Route::get('reports/dtr/{date}', ['uses'=>'DtrController@getDtrReports']);
Route::post('dtr/generate', ['uses'=>'DtrController@postGenerate']);


Route::get('timesheet/{param1?}', ['uses'=>'TimesheetController@getRoute']);
Route::get('purchase', ['uses'=>'PurchaseController@getIndex']);


Route::get('backups/web/{param1?}/{param2?}', ['uses'=>'UploadController@indexWeb']);
Route::get('backups/pos/{param1?}/{param2?}', ['uses'=>'UploadController@indexPos']);
Route::get('backups/files/{param1?}/{param2?}', ['uses'=>'UploadController@indexFiles']);
Route::get('backups/upload', ['uses'=>'UploadController@getBackupUpload']);
Route::get('backups', ['uses'=>'UploadController@index']);

Route::post('upload/postfile', ['as'=>'upload.postfile', 'uses'=>'UploadController@postfile']); // upload to web
Route::put('upload/postfile', ['as'=>'upload.putfile', 'uses'=>'UploadController@putfile']); // move from web to storage
Route::get('download/{param1?}/{param2?}/{param3?}/{param4?}/{param5?}', ['uses'=>'UploadController@getDownload']);


Route::get('timelog/{param1?}/{param2?}', ['uses'=>'TimelogController@getIndex'])
  ->where(['param1'=>'add', 
          'param2'=>'week|[0-9]+']);
Route::post('timelog', ['uses'=>'TimelogController@manualPost']);


Route::get('product/sales', ['uses'=>'SaleController@getDaily']);
Route::get('{brcode}/product/sales', ['uses'=>'SaleController@getDaily']);
Route::get('api/mdl/sales/{id}', ['uses'=>'SaleController@ajaxSales']);
Route::get('api/mdl/purchases/{id}', ['uses'=>'Purchase2Controller@ajaxPurchases']);

Route::get('component/purchases', ['uses'=>'Purchase2Controller@getDaily']);
Route::get('{brcode}/component/purchases', ['uses'=>'Purchase2Controller@getDaily']);
Route::get('api/search/component', ['uses'=>'Purchase2Controller@search']);

/******************* API  *************************************************/
Route::group(['prefix'=>'api'], function(){

Route::get('search/employee', ['uses'=>'EmployeeController@search']);

Route::post('t/employee', ['as'=>'employee.post', 'uses'=>'EmployeeController@post']);
Route::put('t/employee', ['as'=>'employee.put', 'uses'=>'EmployeeController@put']);
Route::get('dt/employee', ['as'=>'employee.dt', 'uses'=>'EmployeeController@dt']);

Route::get('t/purchase', ['uses'=>'PurchaseController@apiGetPurchase']);



Route::post('t/mansked', ['as'=>'mansked.post', 'uses'=>'ManskedController@post']);	
Route::post('c/mansked', ['as'=>'mansked.copy', 'uses'=>'ManskedController@copyMansked']);	

Route::post('t/manskedday', ['as'=>'manday.post', 'uses'=>'ManskeddayController@post']);
Route::put('t/manskedday/{id}', ['as'=>'manday.put', 'uses'=>'ManskeddayController@put']);

});/******* end prefix:api ********/


}); /******* end middeware:auth ********/

// for TK
Route::post('api/timelog', ['as'=>'timelog.post', 'uses'=>'TimelogController@post']);
Route::get('tk', ['as'=>'tk.index','uses'=>'TimelogController@getTkIndex']);
Route::get('api/employee/{field?}/{value?}', ['as'=>'employee.getbyfield', 'uses'=>'EmployeeController@getByField']);





get('upload/process', ['uses'=>'UploadController@processPosBackup']); 
get('upload/ds', ['uses'=>'UploadController@ds']); 






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




Route::controller('datatables', 'DatatablesController', [
    'anyData'  => 'datatables.data',
    'getIndex' => 'datatables',
]);






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
	//$branch = App\Models\Branch::find($id);
	//return str_slug($branch->address);
	$branches = new App\Models\Department;
	return array_flatten($branches->whereNotIn('code', ['KIT'])->get(['id'])->toArray());
	
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

get('test-event', ['uses'=>'UploadController@test']);


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
	return dd(Storage::disk(app()->environment()));
	$directories = Storage::allDirectories(session('user.branchcode'));
	return $directories;
});


get('test', function(){

	/*
	 $path = public_path('uploads'.DS.'mar'.DS.'2016');
	 $to = $path.DS.'test';
	 //if(!is_dir($to))
	 	//mkdir($to, 0775, true);

	//$zip = Zipper::make($path.DS.'GC120915.ZIP')->extractTo($to);
	$zip = Zipper::make($path.DS.'GC120915.ZIP');

	return dd($zip->contains('SALESMTDS.DBF'));
	*/


	return App\Models\Employee::where('branchid', '0C17FE2D78A711E587FA00FF59FBB323')->get();


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
    $message->from('no-reply@giligansrestaurant.com', 'Giligan\'s Web App');

    $message->to('freakyash_02@yahoo.com');
	});
});


get('phpinfoko', function(){
	

	return phpinfo();
});









 

