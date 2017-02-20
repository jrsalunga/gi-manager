<?php namespace App\Traits;

use StdClass;
use App\Models\Branch;
use App\Repositories\DateRange;

trait Itemizable {
  
  public function itemByBranchAndDateRange(Branch $branch, DateRange $dr, $order = 'ASC') {
    
    $arr = [];
    $dss = $this->scopeQuery(function($query) use ($order, $dr) {
              return $query->whereBetween('date', [$dr->fr->format('Y-m-d'), $dr->to->format('Y-m-d')])
                            ->orderBy('date', $order);
          })->findWhere([
            'branchid' => $branch->id
          ]);
    return $dss;
    

    foreach ($dr->dateInterval() as $key => $date) {
      $filtered = $dss->filter(function ($item) use ($date){
          return $item->date->format('Y-m-d') == $date->format('Y-m-d')
                ? $item : null;
      });
      $obj = new StdClass;
      $obj->date = $date;
      $obj->recordset = $filtered->first();
      $arr[$key] = $obj;
    }

    return collect($arr);

  }
}
