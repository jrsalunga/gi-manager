<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\DailySalesRepository as DSRepo;
use Illuminate\Container\Container as App;
use Illuminate\Support\Collection;



class DashboardController extends Controller {

	protected $ds;
	protected $dr;

	public function __construct(DSRepo $dsrepo, DateRange $dr) {
		$this->ds = $dsrepo;
		$this->dr = $dr;

	}




	public function getIndex(Request $request){

		//return $dailysales = $this->ds->paginate(8); 
		$dailysales = $this->ds->getLastestSales($request, 8); 
		return view('dashboard.index', compact('dailysales'));
	}


	public function getAnalytics(Request $request){

		//return dd(is_null($request->input('fr'));

		$fr = !is_null($request->input('fr')) ? carbonCheckorNow($request->input('fr')) : $this->dr->fr;
		$to = !is_null($request->input('to')) ? carbonCheckorNow($request->input('to')) : $this->dr->to;

		if ($to->lt($fr)) {
			$to = Carbon::now();
			$fr = $to->copy()->subDay(30); //$fr = $to->copy()->subDay(7);
		} 

		$this->dr->fr = $fr;
		$this->dr->to = $to;
		$this->dr->date = $to;

		$dailysales = $this->ds->branchByDR($request->user()->branchid, $this->dr);

		return $this->setViewWithDR(view('dashboard.analytics')->with('dailysales', $dailysales)
																		->with('dr', $this->dr));
	}


	







	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
		return $response;
	}
}