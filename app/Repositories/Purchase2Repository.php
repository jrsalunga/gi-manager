<?php namespace App\Repositories;
use DB;
use Carbon\Carbon;
use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Itemizable;
use App\Models\Branch;
use App\Repositories\DateRange;


class Purchase2Repository extends BaseRepository implements CacheableInterface
//class Purchase2Repository extends BaseRepository 
{
  use CacheableRepository, Itemizable;

	public function __construct() {
    parent::__construct(app());

  }

	public function model() {
    return 'App\Models\Purchase2';
  }

  

  public function deleteWhere(array $where){
  	return $this->model->where($where)->delete();
  }

  public function branchByDR(DateRange $dr) {
    return $dss = $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select('purchase.*', 'component.descriptor as component', 'component.uom as uom',
                        'supplier.code as suppliercode', 'supplier.descriptor as supplier',
                        'compcat.code as compcatcode', 'compcat.descriptor as compcat', 
                        'expense.code as expensecode', 'expense.descriptor as expense',
                        'expscat.code as expscatcode', 'expscat.descriptor as expscat')
                    ->orderBy('purchase.date', 'asc')
                    ->orderBy('component.descriptor', 'asc');
    });
  }


  public function brComponentByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('component.descriptor as component, count(purchase.componentid) as tran_cnt, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost'))
                    ->groupBy('purchase.componentid')
                    ->orderBy('purchase.date', 'asc');
                    //->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brCompCatByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('compcat.descriptor as compcat, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost'))
                    ->groupBy('compcat.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brExpenseByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('expense.code as expensecode, expense.descriptor as expense, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost'))
                    ->groupBy('expense.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brExpsCatByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('expscat.code as expscatcode, expscat.descriptor as expscat, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost'))
                    ->groupBy('expscat.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }

  public function brSupplierByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('supplier.code as code, supplier.descriptor, sum(purchase.qty) as qty, sum(purchase.tcost) as tcost'))
                    ->groupBy('supplier.id')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }


  public function brPaymentByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('component', 'component.id', '=', 'purchase.componentid')
                    ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplierid')
                    ->leftJoin('compcat', 'compcat.id', '=', 'component.compcatid')
                    ->leftJoin('expense', 'expense.id', '=', 'compcat.expenseid')
                    ->leftJoin('expscat', 'expscat.id', '=', 'expense.expscatid')
                    ->select(DB::raw('purchase.terms, sum(purchase.tcost) as tcost'))
                    ->groupBy('purchase.terms')
                    ->orderBy(DB::raw('sum(purchase.tcost)'), 'desc');
    });
  }


  public function componentAverageByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('purchase.date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->where('purchase.qty', '>', 0)
                    ->where('purchase.ucost', '>', 0)
                    ->leftJoin('hr.branch', 'branch.id', '=', 'purchase.branchid')
                    ->select(DB::raw('branch.code, SUM(purchase.qty) as tot_qty, SUM(purchase.tcost) as tcost, (SUM(purchase.tcost)/SUM(purchase.qty)) as ave, MAX(purchase.ucost) as ucost_max, MIN(purchase.ucost) as ucost_min, MAX(purchase.qty) as qty_max, MIN(purchase.qty) as qty_min, count(purchase.id) as trancnt'))
                    ->groupBy('purchase.branchid')
                    ->orderBy('branch.code');
    });
  }








  

}