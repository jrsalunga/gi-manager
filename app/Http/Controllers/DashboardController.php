<?php namespace App\Http\Controllers;

use Carbon\Carbon;
#use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\BackupRepository as Backup;
use Illuminate\Container\Container as App;
use Illuminate\Support\Collection;



class DashboardController extends Controller {

	protected $ds;
	protected $dr;
	protected $backup;

	public function __construct(DSRepo $dsrepo, DateRange $dr, Backup $backup) {
		$this->ds = $dsrepo;
		$this->dr = $dr;
		$this->backup = $backup;

	}




	public function getIndex(Request $request){
		$inadequates = $this->backup->inadequateBackups();
		$backup = $this->backup->latestBackupStatus();
		//return $dailysales = $this->ds->paginate(8); 
		$dailysales = $this->ds->getLastestSales($request, 8); 
		return view('dashboard.index', compact('dailysales'))->with('backup', $backup)->with('inadequates', $inadequates);
	}


	


	







	private function setViewWithDR($view){
		$response = new Response($view->with('dr', $this->dr));
		$response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
		$response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
		$response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
		return $response;
	}
}