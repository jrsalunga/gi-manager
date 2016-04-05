<?php namespace App\Repositories;

use Carbon\Carbon;
use StdClass;
use App\Repositories\Repository;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;
use App\Repositories\Criterias\ByBranchCriteria;

use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;


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
  			return $query->orderBy('year','desc')
  									->orderBy('month','desc')
  									->orderBy('uploaddate','desc')
  									->orderBy('filename','desc');
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




  
  

    




}