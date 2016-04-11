<?php namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
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

	public function getIndex() {
		return $this->purchase->paginate(5);
	}

	private function getFieldTotal($data, $field, $sum) {
		$arr = [];

		foreach ($data as $value) {
			if (array_key_exists($value->{$field} ,$arr))
				$arr[$value->{$field}] += $value->{$sum};
			else
				$arr[$value->{$field}] = $value->{$sum};
		}
		return $arr;
	}

	private function getExpenseTotal($data, $sum) {
		$arr = [];

		foreach ($data as $value) {
			$key = substr($value->supno, 0, 2);
			if (array_key_exists($key ,$arr))
				$arr[$key] += $value->tcost;
			else
				$arr[$key] = $value->tcost;
		}
		return $arr;
	}

	public function apiGetPurchase(Request $request) {
		
		//$categories 
		$date = carbonCheckorNow($request->input('date'));
		$data = $this->getPurchaseByDate($date);

		$json = [
			'status' => 'success',
			'code' => 200,
			'data' => [
				'items' => [
					'date' => $date->format('Y-m-d'),
					'data' => $data
				],
				'stats' => [
					'categories' 	=> $this->getFieldTotal($data, 'catname', 'tcost'),
					'expenses' 		=> $this->getExpenseTotal($data, 'tcost'),
					'suppliers' 	=> $this->getFieldTotal($data, 'supname', 'tcost')
				]
			]
		];

		if ($request->ajax()) {
			return response()->json($json);
			return response()->json(['status' => 'success',
														'code' => 200,
														'date' => $date->format('Y-m-d'),
														'data' => $data]);
    } else {
      if (intval($request->input('download'))===1) {
      	return $this->export($data, $date);
      } else 
      	return $data;
    }
	}



	private function getPurchaseByDate(Carbon $date) {
		return $this->purchase->findWhere(['date'=>$date->format('Y-m-d')]);
	}

	private function export($data, Carbon $date, $ext='xlsx') {
		$ext = in_array($ext, ['xls', 'xlsx', 'csv']) ? $ext:'xlsx';
  	$filename = 'PO-'.session('user.branchcode').'-'.$date->format('Ymd').'-'.Carbon::now()->format('His');
  	
  	$output = [];
		array_push($output, ['Component', 'Category', 'UoM', 'Qty', 'Unit Cost', 'Total Cost', 'Sup Code', 'Sup Name', 'Terms', 'VAT']);	
		
		foreach ($data as $d) {
			array_push($output, [
				$d->comp, $d->catname, $d->unit, $d->qty, $d->ucost, $d->tcost, $d->supno, $d->supname, $d->terms, $d->vat
			]);	
		}


  	return Excel::create($filename, function($excel) use ($output, $date) {
			$excel->sheet('PO-'.$date->format('Y-m-d'), function($sheet) use ($output) {
		    $sheet->fromArray($output, null, 'A1', false, false);
		  });
		})->export($ext);
	}



}