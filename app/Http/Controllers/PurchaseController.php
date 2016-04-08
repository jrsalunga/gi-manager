<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\PurchaseRepository as Purchase;



class PurchaseController extends Controller {

	protected $dr;
	protected $purchase;

	public function __construct(Purchase $purchase, DateRange $dr) {
		$this->purchase = $purchase;
		$this->dr = $dr;

	}



	public function getIndex(){
		return $this->purchase->paginate(5);
	}


	public function apiGetPurchase(Request $request) {

		$data = $this->purchase->findWhere(['date'=>$request->input('date')]);

		return response()->json(['status' => 'success',
														'code' => 200,
														'data' => $data]);
	}



}