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
    $products = $this->sale->skipCache()->paginate(10);
    return view('index');
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


    $data = [
      'ds' => $ds,
      'sales' => $sales,
      'products' => $products,
      'prodcats' => $prodcats,
      'menucats' => $menucats
    ];

    return $data;
  }



}