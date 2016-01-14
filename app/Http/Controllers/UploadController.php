<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\PosUpload;
use App\Models\DailySales;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use App\Repositories\StorageRepository;
use App\Repositories\Filters\WithBranch;
use App\Repositories\Filters\ByBranch;
use App\Repositories\PosUploadRepository;
use Illuminate\Support\Facades\Storage;
use Dflydev\ApacheMimeTypes\PhpRepository;
use File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as Http404;
use App\Events\UserLoggedIn;

class UploadController extends Controller {

	protected $files;
	protected $pos;
	protected $fs;
	protected $branch;
	protected $mime;
	protected $backup;
	public $override = false;

	public function __construct(Request $request, PhpRepository $mimeDetect, PosUploadRepository $posuploadrepo){
		$this->branch = session('user.branchcode');
		$this->mime = $mimeDetect;
		$this->fs = new Filesystem;
		$this->files = new StorageRepository($mimeDetect, 'files.'.app()->environment());
		$this->pos = new StorageRepository($mimeDetect, 'pos.'.app()->environment());
		$this->web = new StorageRepository($mimeDetect, 'web');
		$this->backup = $posuploadrepo;
		//$this->backup->pushFilters(new WithBranch(['code', 'descriptor', 'id']));
  	$this->backup->pushFilters(new ByBranch($request));

		
		$this->path['temp'] = strtolower(session('user.branchcode')).DS.now('year').DS;
		$this->path['web'] = config('gi-dtr.upload_path.web').strtolower(session('user.branchcode')).DS.now('year').DS;
	
		
	}

	public function test(Request $request){
		event(new UserLoggedIn($request));
	}


	public function index(Request $request){
		//return view('backups.index');
		return redirect('backups/pos');
	}

	public function getBackupUpload(Request $request){
		return view('backups.upload');
	}

	private function setUri($param1=null, $param2=null){
		//$uri = '';
		//$uri .= (is_null($param1) && is_year($param1)) ? $param1 : now('Y');

		if(!is_null($param2) && is_month($param2)){
			if(!is_null($param1) && is_year($param1)){
				$uri = '/'.$param1.'/'.$param2;
			} else {
				throw new Http404("Error Processing Request");
			}
		} else if(!is_null($param1) && is_year($param1)) {
			$uri = '/'.$param1;
		} else {
			$uri = '';
		}
		 return $uri;
	}

	public function indexPos(Request $request, $param1=null, $param2=null) {


		$folder = '/pos'.$this->setUri($param1, $param2);
		$data = $this->pos->folderInfo($folder);
		//return $data;
		return view('backups.filelist')->with('data', $data)->with('tab', 'pos');
		return dd($data);
	} 

	public function indexFiles(Request $request, $param1=null, $param2=null) {

		$folder = '/files'.$this->setUri($param1, $param2);
		$data = $this->files->folderInfo($folder);
		return view('backups.filelist')->with('data', $data)->with('tab', 'files');
		return dd($data);
		//return view('upload.index');
	} 

	public function indexWeb(Request $request, $param1=null, $param2=null) {

		$folder = '/web'.$this->setUri($param1, $param2);
		$data = $this->web->folderInfo($folder);
		return dd($data);
	} 


	private function getStorageType($filename){
		if(strtolower(pathinfo($filename, PATHINFO_EXTENSION))==='zip')
				return $this->pos;
		
		return $this->files;
	}




	/* move file from web to maindepot
	*/
	public function putfile(Request $request) {

		$yr = empty($request->input('year')) ? now('Y'):$request->input('year');
		$mon = empty($request->input('month')) ? now('M'):$request->input('month');

		$filepath = $this->path['temp'].$request->input('filename');
		$storage_path = $this->branch.DS.$yr.DS.$mon.DS.$request->input('filename'); 

		if($this->web->exists($filepath)){
			$storage = $this->getStorageType($filepath);

			

			try {
	      $storage->moveFile($this->web->realFullPath($filepath), $storage_path, true); // false = override file!
	    }catch(\Exception $e){
					return redirect('/backups/upload')->with('alert-error', $e->getMessage());
	    }

	    /*** if backup file ****/
	    if(starts_with($storage->getType(),'pos') && starts_with($request->input('filename'),'GC')) {

		    $res = $this->createPosUpload($storage_path, $request);
		    if(!$res)
					return redirect('/backups/upload')
									->with('alert-error', 'File: '.$request->input('filename').' unable to create record');
		    
				if(!$this->processDailySales($storage_path, $res))
					return redirect('/backups/upload')
									->with('alert-error', 'File: '.$request->input('filename').' unable to extract');

				return redirect('/backups/upload')->with('alert-success', 'File: '.$request->input('filename').' successfully uploaded and processed daily sales!');
	    } else {
				return redirect('/backups/upload')->with('alert-success', 'File: '.$request->input('filename').' successfully uploaded!');
	    }
			
		
		} else {
			$this->logAction('move:error', 'user:'.$request->user()->username.' '.$request->input('filename').' message:try_again');
			return redirect('/backups/upload')->with('alert-error', 'File: '.$request->input('filename').' do not exist! Try to upload again..');
		}
	} 


	private function logAction($action, $log) {
		$logfile = base_path().DS.'logs'.DS.now().'-log.txt';
		$new = file_exists($logfile) ? false : true;
		if($new){
			$handle = fopen($logfile, 'w');
			chmod($logfile, 0775);
		} else
			$handle = fopen($logfile, 'a');

		$ip = $_SERVER['REMOTE_ADDR'];
		$brw = $_SERVER['HTTP_USER_AGENT'];
		$content = date('r')." | {$ip} | {$action} | {$log} \t {$brw}\n";
    fwrite($handle, $content);
    fclose($handle);
	}	

 	// depricated
	public function setPath($filename){
		if(strtolower(pathinfo($filename, PATHINFO_EXTENSION))==='zip')
				return 'pos'.DS.$this->path['temp'].DS;
		else
				return 'files'.DS.$this->path['temp'].DS;
	}



	/* upload to web from ajax 
	*/
	public function postfile(Request $request) {
		
		if($request->file('pic')->isValid()) {

			$filename = rawurldecode($request->file('pic')->getClientOriginalName());
			
			//$ext = $request->file('pic')->guessExtension();
			//$mimetype = $request->file('pic')->getClientMimeType();
			
			$file = File::get($request->file('pic'));

			$path = $this->path['temp'].$filename;

			$res = $this->web->saveFile($path, $file, false); // false = override file!


			if($res===true){
				return json_encode(['status'=>'success', 
													'code'=>'200', 
													'message'=>$res, 
													'year'=>$request->input('year'),
													'month'=>$request->input('month')]);
			} else {
				return json_encode(['status'=>'warning', 
													'code'=>'201', 
													'message'=>$res, 
													'year'=>$request->input('year'),
													'month'=>$request->input('month')]);
			}
			

		} else {
			return redirect('/upload/backup')
								->with('alert-error', 'File: '.$request->input('filename').' corrupted! Try to upload again..');
		}
		
	}




	public function getDownload(Request $request, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL, $p5=NULL){
    

    if(is_null($p2) || is_null($p2) || is_null($p3) || is_null($p4) || is_null($p5)){
    	throw new Http404("Error Processing Request");
    }
    

    //return 'tada!';

    $path = $p2.'/'.$p3.'/'.$p4.'/'.$p5;

		$storage = $this->getStorageType($path);

		$file = $storage->get($path);
		$mimetype = $storage->fileMimeType($path);

		//return dd($file);

		//return response()->download($pathToFile, $name, $headers);


    $response = \Response::make($file, 200);
	 	$response->header('Content-Type', $mimetype);
  	$response->header('Content-Disposition', 'attachment; filename="'.$p5.'"');

	  return $response;

  }


  // for /put/upload/postfile @ $this->putfile()
  //public function processPosBackup($src, $ip){
  public function createPosUpload($src, Request $request){

	 	$data = [
	 		'branchid' => session('user.branchid'),
    	'filename' => $request->input('filename'),
    	'year' => $request->input('year'),
    	'month' => $request->input('month'),
    	'size' => $this->pos->fileSize($src),
    	'mimetype' => $this->pos->fileMimeType($src),
    	'terminal' => $request->ip(),
    	'remarks' => $src,
    	'userid' => $request->user()->id
    ];

    return $this->backup->create($data)?:false;
  }

  public function extract($src, $pwd=NULL){

  	return $this->backup->extract($src, $pwd);
  }

  public function ds(Request $request) {
  	
  	//return $this->backup->lastRecord();
  	$this->backup->ds->pushFilters(new WithBranch(['code', 'descriptor', 'id']));
  	return $this->backup->ds->lastRecord();
  }

  public function processDailySales($filepath, PosUpload $posupload){
  	$this->backup->extract($filepath, 'admate');
  	$res = $this->backup->postDailySales();
  	if($res) 
  		$this->backup->update(['processed'=>1], $posupload->id);
  	
  	$this->backup->removeExtratedDir();
  	return $res;
  }

	
}