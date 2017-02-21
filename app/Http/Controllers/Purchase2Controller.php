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
use App\Models\Component;
use App\Models\Compcat;
use App\Models\Expense;
use App\Models\Expscat;
use App\Models\Supplier;

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


    $where = [];
    $fields = ['component', 'supplier', 'expense', 'expscat', 'compcat'];
    
    $filter = new StdClass;
    if($request->has('itemid') && $request->has('table') && $request->has('item')) {
      
      $id = strtolower($request->input('itemid'));
      $table = strtolower($request->input('table'));

      $c = '\App\Models\\'.ucfirst($table);
      $i = $c::find($id);

      if (strtolower($request->input('item'))==strtolower($i->descriptor)) {
        $item = $request->input('item');
      
        if(is_uuid($id) && in_array($table, $fields))
          $where[$table.'.id'] = $id;
        else if($table==='payment')
          $where['purchase.terms'] = $id;

        $filter->table = $table;
        $filter->id = $id;
        $filter->item = $item;
      } else {
        $filter->table = '';
        $filter->id = '';
        $filter->item = '';
      }
    } else {
      $filter->table = '';
      $filter->id = '';
      $filter->item = '';
    }
    
    $this->dr->fr = c();
    $this->dr->to = c();

    $res = $this->dr->setDateRangeMode($request, 'daily');

    $where['purchase.branchid'] = $request->user()->branchid;

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

    return $this->setDailyViewVars('component.purchased.daily', $purchases, $filter, $components, $compcats, $expenses, $expscats, $suppliers, $payments);
  
  }


  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }


  private function setDailyViewVars($view, $purchases=null, $filter=null,
    $components=null, $compcats=null, $expenses=null, $expscats=null, $suppliers=null, $payments=null) {

    return $this->setViewWithDR(view($view)
                ->with('purchases', $purchases)
                ->with('components', $components)
                ->with('compcats', $compcats)
                ->with('expenses', $expenses)
                ->with('expscats', $expscats)
                ->with('suppliers', $suppliers)
                ->with('payments', $payments)
                ->with('filter', $filter));
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


  public function search(Request $request) {


    $arr = [];

    if($request->has('q')) {
      
      $q = $request->input('q');
      $branchid = $request->user()->branchid;
      
      $components = Component::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($components as $component) {
        array_push($arr, ['table'=>'component', 'item'=>$component->descriptor, 'id'=>strtolower($component->id)]);
      }

      $compcats = Compcat::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($compcats as $compcat) {
        array_push($arr, ['table'=>'compcat', 'item'=>$compcat->descriptor, 'id'=>strtolower($compcat->id)]);
      }

      $expenses = Expense::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($expenses as $expense) {
        array_push($arr, ['table'=>'expense', 'item'=>$expense->descriptor, 'id'=>strtolower($expense->id)]);
      }

      $expscats = Expscat::where('descriptor', 'like', '%'.$q.'%')->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($expscats as $expscat) {
        array_push($arr, ['table'=>'expscat', 'item'=>$expscat->descriptor, 'id'=>strtolower($expscat->id)]);
      }

      $suppliers = Supplier::where('descriptor', 'like', '%'.$q.'%')->where('branchid', $branchid)->orderBy('descriptor')->get(['descriptor', 'id']);
      foreach ($suppliers as $supplier) {
        array_push($arr, ['table'=>'supplier', 'item'=>$supplier->descriptor, 'id'=>strtolower($supplier->id)]);
      }

      //$payments = ['110A573E855511E68FF47E841D02B37D'=>'cash', '19ADA60B855511E68FF47E841D02B37D'=>'check'];
      $payments = ['c'=>'cash', 'k'=>'check'];

      $s = preg_grep("/^".strtolower($q)."/", $payments);
      if($s) {
        $k = key($s);
        array_push($arr, ['table'=>'payment', 'item'=>ucwords($s[$k]), 'id'=>strtolower($k)]);
      }

    }


    //return $arr;


      if($request->ajax())
        return $arr;
      else
        return abort('404');

   }


}