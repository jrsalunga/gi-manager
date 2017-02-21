<?php namespace App\Http\Controllers;

use StdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\SalesmtdRepository as SalesmtdRepo;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Repositories\Criterias\ByBranch2;
use App\Repositories\Criterias\ByBranchCriteria;

use App\Models\Product;
use App\Models\Prodcat;
use App\Models\Menucat;


class SaleController extends Controller { 

	protected $sale;
	protected $dr;
  protected $ds;
  protected $branch;

  public function __construct(SalesmtdRepo $sale, DateRange $dr, DSRepo $ds) {
    $this->sale = $sale;
    $this->sale->pushCriteria(new ByBranch2(request()));
    $this->dr = $dr;
    $this->ds = $ds;
    $this->ds->pushCriteria(new ByBranchCriteria(request()));

  }

  public function getDaily(Request $request) {

    $this->dr->setDateRangeMode($request, 'daily');


    
    $where = [];
    $fields = ['menucat', 'prodcat', 'product'];
    
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
      $sales = $this->sale->skipCache()->byDateRange($this->dr)->findWhere($where);


    } else {
      $filter->table = '';
      $filter->id = '';
      $filter->item = '';
      $sales = null;
      
      if ($this->dr->fr->eq($this->dr->to))
        $sales = $this->sale->skipCache()->byDateRange($this->dr)->findWhere($where);
    
    }

    //$where['salesmtd.branch_id'] = $branch->id;
    $where = [];    

    $ds = $this->ds
          //->skipCache()
          ->sumByDateRange($this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d'))
          ->all();

    $groupies = $this->aggregateGroupies($this->sale->brGroupies($this->dr)->all());
    
    $products = $this->sale
          ->skipCache()
          ->brProductByDR($this->dr)
          ->findWhere($where);

    $prodcats = $this->sale
          //->skipCache()
          ->brProdcatByDR($this->dr)
          ->findWhere($where);

    $menucats = $this->sale
          //->skipCache()
          ->brMenucatByDR($this->dr)
          ->findWhere($where);

    return $this->setDailyViewVars('product.sales.daily', $filter, $sales, $ds[0], $products, $prodcats, $menucats, $groupies);
  }

  private function aggregateGroupies($grps) {
    $arr = [];

    foreach ($grps as $key => $value) {
      if(array_key_exists($value['group'], $arr)) {
        $arr[$value['group']]['qty'] += $value['qty'];
        $arr[$value['group']]['grsamt'] += $value['grsamt'];
      } else {
        $arr[$value['group']]['group'] = $value['group'];
        $arr[$value['group']]['qty'] = $value['qty'];
        $arr[$value['group']]['grsamt'] = $value['grsamt'];
      }
    }

    return $arr;
  }



  private function setDailyViewVars($view, $filter=null, $sales=null, $ds=null, $products=null, $prodcats=null, $menucats=null, $groupies=null) {

    return $this->setViewWithDR(view($view)
                ->with('filter', $filter)
                ->with('sales', $sales)
                ->with('ds', $ds)
                ->with('products', $products)
                ->with('prodcats', $prodcats)
                ->with('groupies', $groupies)
                ->with('menucats', $menucats));
  }



  private function setViewWithDR($view){
    $response = new Response($view->with('dr', $this->dr));
    $response->withCookie(cookie('to', $this->dr->to->format('Y-m-d'), 45000));
    $response->withCookie(cookie('fr', $this->dr->fr->format('Y-m-d'), 45000));
    $response->withCookie(cookie('date', $this->dr->date->format('Y-m-d'), 45000));
    return $response;
  }




  public function ajaxSales(Request $request, $id) {
    if ($request->ajax()) {
      $data = $this->modalSalesData($request, $id);
      return response()->view('analytics.modal.mdl-sales', compact('data'))
                  ->header('Content-Type', 'text/html');
    }
    return abort('404');
  }

   private function modalSalesData(Request $request, $id) {

    $this->dr->setDateRangeMode($request, 'daily');

    

    $where = [];

    $ds = $this->ds->find($id);

    $sales = $this->sale
          ->skipCache()
          ->byDateRange($this->dr)
          ->orderBy('ordtime')
          ->findWhere($where);

    $products = $this->sale
          ->skipCache()
          ->brProductByDR($this->dr)
          ->findWhere($where);

    $prodcats = $this->sale
          ->skipCache()
          ->brProdcatByDR($this->dr)
          ->findWhere($where);

    $menucats = $this->sale
          ->skipCache()
          ->brMenucatByDR($this->dr)
          ->findWhere($where);

    $groupies = $this->aggregateGroupies($this->sale->brGroupies($this->dr)->all());


    $data = [
      'ds' => $ds,
      'sales' => $sales,
      'products' => $products,
      'prodcats' => $prodcats,
      'menucats' => $menucats,
      'groupies' => $groupies
    ];

    return $data;
  }



}