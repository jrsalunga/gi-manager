<?php namespace App\Http\Controllers;

use StdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\Purchase2Repository as PurchaseRepo;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\Criterias\ByBranchCriteria;

class Purchase2Controller extends Controller { 

	protected $purchase;
  protected $dr;
  protected $ds;

  public function __construct(PurchaseRepo $purchase, DateRange $dr, DSRepo $ds) {
    $this->purchase = $purchase;
    //$this->purchase->pushCriteria(new ByBranchCriteria(request()));
    $this->ds = $ds;
    $this->ds->pushCriteria(new ByBranchCriteria(request()));
    $this->dr = $dr;
  }

  public function getDaily(Request $request) { 
  	$purchases = $this->purchase->skipCache()->paginate(10);
    return view('index');
  }

  public function ajaxPurchases(Request $request, $id) {
   	if($request->ajax()) {
      $data = $this->modalPurchasesData($request, $id);
      return response()->view('analytics.modal.mdl-purchases', compact('data'))->header('Content-Type', 'text/html');
    }
    return abort('404');
  }

  public function modalPurchasesData(Request $request, $id) {
    
    $this->dr->setDateRangeMode($request, 'daily');

    $where['purchase.branchid'] = $request->user()->branchid;

    $ds = $this->ds->find($id);

    $purchases = $this->purchase
    								->skipCache()
                    ->branchByDR($this->dr)
                    ->findWhere($where);
    $components = $this->purchase
    							->skipCache()
                  ->brComponentByDR($this->dr)
                  ->findWhere($where);
    $compcats = $this->purchase
    							->skipCache()
                  ->brCompCatByDR($this->dr)
                  ->findWhere($where);
    $expenses = $this->purchase
    							->skipCache()
                  ->brExpenseByDR($this->dr)
                  ->findWhere($where); 
    $expscats = $this->purchase
    							->skipCache()
                  ->brExpsCatByDR($this->dr)
                  ->findWhere($where);
    $suppliers = $this->purchase
    							->skipCache()
                  ->brSupplierByDR($this->dr)
                  ->findWhere($where); 
    $payments = $this->purchase
    							->skipCache()
                  ->brPaymentByDR($this->dr)
                  ->findWhere($where); 

    return [
      'ds' => $ds,
      'purchases' => $purchases,
      'components' => $components,
      'compcats' => $compcats,
      'expenses' => $expenses,
      'expscats' => $expscats,
      'suppliers' => $suppliers,
      'payments' => $payments
    ];    
  }


}