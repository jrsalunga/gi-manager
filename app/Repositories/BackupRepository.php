<?php namespace App\Repositories;

use StdClass;
use Carbon\Carbon;
use App\Helpers\Locator;
use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;
use App\Repositories\Criterias\ByBranchCriteria;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Models\Branch;

//class BackupRepository extends BaseRepository implements CacheableInterface
class BackupRepository extends BaseRepository 
{
  //use CacheableRepository;

	public function __construct(App $app, Collection $collection) {
      parent::__construct($app, $collection);

      $this->pushCriteria(new ByBranchCriteria(request()))
  		->scopeQuery(function($query){
  			return $query->orderBy('uploaddate','desc');
			});
  }


	public function model() {
    return 'App\\Models\\Backup';
  }


  public function latestBackup() {
  	return $this->scopeQuery(function($query){
  		return $query
        ->orderBy('year','desc')
				->orderBy('month','desc')
				->orderBy('filename','desc')
        ->orderBy('uploaddate','desc');
			})->first();
  }

  public function latestBackupStatus(){
  	$backup = new StdClass;
  	$file = $this->latestBackup();

  	if(is_null($file))
  		return $file;

  	$backup->diffInDays = $file->date->diffInDays(Carbon::now(), false); 
  	$backup->file = $file;
  	$backup->diffForHumans = diffForHumans($file->uploaddate);

  	if($backup->diffInDays=='0')
  		$backup->bg = 'text-success';
  	else if($backup->diffInDays=='1')
  		$backup->bg = 'text-info';
  	else 
  		$backup->bg = 'text-danger';


  	return $backup;

  }

  public function inadequateBackups($fr=null, $to=null) {
    if (is_null($fr) && is_null($to)) {
      $to = Carbon::now();
      $fr = $to->copy()->subDays(30);
    }

    $fr = Carbon::parse($fr);
    $to = Carbon::parse($to);

    if ($fr->gt($to))
      return false;

    $locator = new Locator('pos');

    $branch = Branch::where('code', strtoupper(substr(request()->user()->name, 0, 3)))->first();
    $arr = [];
    $o = $fr->copy();
    do {
      $path = strtoupper(substr(request()->user()->name, 0, 3)).DS.$o->format('Y').DS.$o->format('m').DS.'GC'.$o->format('mdy').'.ZIP';
      if (!$locator->exists($path) && Carbon::parse(now())->gt($o) && Carbon::parse($branch->opendate)->lt($o))
        array_push($arr, Carbon::parse($o->format('Y-m-d').' 00:00:00'));
    } while ($o->addDay() <= $to);
   
    return $arr;

  }




  
  

    




}