<?php namespace App\Repositories;
use DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Repositories\ProductRepository;
use App\Traits\Repository as RepoTrait;

class SalesmtdRepository extends BaseRepository implements CacheableInterface
//class SalesmtdRepository extends BaseRepository 
{
  use CacheableRepository, RepoTrait;
  
  protected $order = ['orddate', 'ordtime', 'recno'];

  public function model() {
    return 'App\\Models\\Salesmtd';
  }





  public function byDateRange(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('salesmtd.orddate', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->leftJoin('product', 'product.id', '=', 'salesmtd.product_id')
                    ->leftJoin('prodcat', 'prodcat.id', '=', 'product.prodcat_id')
                    ->leftJoin('menucat', 'menucat.id', '=', 'product.menucat_id')
                    ->select('salesmtd.*', 'product.code as productcode', 'product.descriptor as product', 'product.unit as uom',
                        'prodcat.code as prodcatcode', 'prodcat.descriptor as prodcat', 
                        'menucat.code as menucatcode', 'menucat.descriptor as menucat');
    })->order($this->order);
  }


  public function brProductByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('salesmtd.orddate', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                   ->leftJoin('product', 'product.id', '=', 'salesmtd.product_id')
                    ->leftJoin('prodcat', 'prodcat.id', '=', 'product.prodcat_id')
                    ->leftJoin('menucat', 'menucat.id', '=', 'product.menucat_id')
                    ->select(DB::raw('product.descriptor as product, sum(salesmtd.qty) as txn, sum(salesmtd.qty) as qty, sum(salesmtd.grsamt) as grsamt,
                        sum(salesmtd.netamt) as netamt, prodcat.descriptor as prodcat, menucat.descriptor as menucat'))
                    ->groupBy('salesmtd.product_id')
                    //->orderBy(DB::raw('sum(salesmtd.netamt)'), 'desc');
                    ->orderBy(DB::raw('ordtime'), 'asc');
    })->skipOrder();
  }

  public function brProdcatByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('salesmtd.orddate', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                   ->leftJoin('product', 'product.id', '=', 'salesmtd.product_id')
                    ->leftJoin('prodcat', 'prodcat.id', '=', 'product.prodcat_id')
                    ->leftJoin('menucat', 'menucat.id', '=', 'product.menucat_id')
                    ->select(DB::raw('prodcat.descriptor as prodcat, count(salesmtd.qty) as txn, sum(salesmtd.qty) as qty, sum(salesmtd.grsamt) as grsamt,
                        sum(salesmtd.netamt) as netamt, menucat.descriptor as menucat'))
                    ->groupBy('prodcat.descriptor')
                    ->orderBy(DB::raw('sum(salesmtd.netamt)'), 'desc');
    })->skipOrder();
  }

  public function brMenucatByDR(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('salesmtd.orddate', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                   ->leftJoin('product', 'product.id', '=', 'salesmtd.product_id')
                    ->leftJoin('prodcat', 'prodcat.id', '=', 'product.prodcat_id')
                    ->leftJoin('menucat', 'menucat.id', '=', 'product.menucat_id')
                    ->select(DB::raw('menucat.descriptor as menucat, count(salesmtd.qty) as txn, sum(salesmtd.qty) as qty, sum(salesmtd.grsamt) as grsamt,
                        sum(salesmtd.netamt) as netamt, prodcat.descriptor as prodcat'))
                    ->groupBy('menucat.descriptor')
                    ->orderBy(DB::raw('sum(salesmtd.netamt)'), 'desc');
    })->skipOrder();
  }


  public function brGroupies(DateRange $dr) {
    return $this->scopeQuery(function($query) use ($dr) {
      return $query->whereBetween('salesmtd.orddate', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->where('salesmtd.group', '<>', '')
                    ->select(DB::raw('salesmtd.group, group_cnt as qty, sum(salesmtd.grsamt) as grsamt, cslipno'))
                    ->groupBy('salesmtd.cslipno')
                    ->orderBy(DB::raw('salesmtd.group'), 'asc');
    })->skipOrder();
  }

  public function menucatByDR(DateRange $dr, $menucatid=null) {
    return $this->scopeQuery(function($query) use ($dr, $menucatid) {
      return $query->whereBetween('salesmtd.orddate', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                    ->where('product.menucat_id', $menucatid)
                    ->leftJoin('product', 'product.id', '=', 'salesmtd.product_id')
                    ->select(DB::raw('product.descriptor as product, product.code as productcode, salesmtd.*, 
                      salesmtd.qty as qty, salesmtd.grsamt as grsamt, salesmtd.netamt as netamt, cslipno'))
                    ->groupBy('salesmtd.cslipno')
                    ->orderBy('product.descriptor');
    })->skipOrder();
  }

  


	

}