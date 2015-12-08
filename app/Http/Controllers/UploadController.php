<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use App\Repositories\StorageRepository;
use Illuminate\Support\Facades\Storage;
use Dflydev\ApacheMimeTypes\PhpRepository;
use File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as Http404;

class UploadController extends Controller {

	protected $files;
	protected $pos;
	protected $fs;
	protected $branch;
	protected $mime;

	public function __construct(PhpRepository $mimeDetect){
		$this->branch = session('user.branchcode');
		$this->mime = $mimeDetect;
		$this->fs = new Filesystem;
		$this->files = new StorageRepository($mimeDetect, 'files.'.app()->environment());
		$this->pos = new StorageRepository($mimeDetect, 'pos.'.app()->environment());
		$this->web = new StorageRepository($mimeDetect, 'web');
		
		$this->path['temp'] = strtolower(session('user.branchcode')).DIRECTORY_SEPARATOR.now('year').DIRECTORY_SEPARATOR;
		$this->path['web'] = config('gi-dtr.upload_path.web').strtolower(session('user.branchcode')).DIRECTORY_SEPARATOR.now('year').DIRECTORY_SEPARATOR;
	
		
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
		return $data;
	} 


	private function getStorageType($filename){
		if(strtolower(pathinfo($filename, PATHINFO_EXTENSION))==='zip')
				return $this->pos;
		
		return $this->files;
	}

	public function putfile(Request $request) {

		$yr = empty($request->input('year')) ? now('Y'):$request->input('year');
		$mon = empty($request->input('month')) ? now('M'):$request->input('month');

		$filepath = $this->path['temp'].$request->input('filename');
		$storage_path = $this->branch.DIRECTORY_SEPARATOR.$yr.DIRECTORY_SEPARATOR.$mon.DIRECTORY_SEPARATOR.$request->input('filename'); 

		if($this->web->exists($filepath)){
			$storage = $this->getStorageType($filepath);

			try {
	      $storage->moveFile($this->web->realFullPath($filepath), $storage_path, true);
	    }catch(\Exception $e){
					return redirect('/backups/upload')->with('alert-error', $e->getMessage());
	    }
			
			return redirect('/backups/upload')->with('alert-success', 'File: '.$request->input('filename').' successfully uploaded!');


			
			
			
		} else {
			$this->logAction('move:error', 'user:'.$request->user()->username.' '.$request->input('filename').' message:try_again');
			return redirect('/backups/upload')->with('alert-error', 'File: '.$request->input('filename').' do not exist! Try to upload again..');
		}




		if($this->fs->exists($this->path['temp'].$request->input('filename'))){
			if($this->fs->exists($this->path['storage'].$request->input('filename'))){ 
				$this->logAction('move:error', 'user:'.$request->user()->username.' '.$request->input('filename').' message:file_exist');
				return redirect('/upload/backup')->with('alert-error', 'File: '.$request->input('filename').' exist!');
			} else {

				if(!is_dir($this->path['storage']))
					mkdir($this->path['storage'], 0775, true);

				try {
					File::move($this->path['temp'].$request->input('filename'), $this->path['storage'].$request->input('filename'));
				}catch(\Exception $e){
					$this->logAction('move:error', 'user:'.$request->user()->username.' '.$request->input('filename').' message:'.$e->getMessage());
					return redirect('/upload/backup')->with('alert-error', $e->getMessage());
				}

				$this->logAction('move:success', 'user:'.$request->user()->username.' '.$request->input('filename'));
				return redirect('/upload/backup')->with('alert-success', 'File: '.$request->input('filename').' successfully uploaded!');
			}
		} else {
			$this->logAction('move:error', 'user:'.$request->user()->username.' '.$request->input('filename').' message:try_again');
			return redirect('/upload/backup')->with('alert-error', 'File: '.$request->input('filename').' do not exist! Try to upload again..');
		}
	} 


	private function logAction($action, $log) {
		$logfile = base_path().DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.now().'-log.txt';
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
				return 'pos'.DIRECTORY_SEPARATOR.$this->path['temp'].DIRECTORY_SEPARATOR;
		else
				return 'files'.DIRECTORY_SEPARATOR.$this->path['temp'].DIRECTORY_SEPARATOR;
	}

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
			
			

			/*
			if($this->fs->exists($this->path['temp'].$filename))
				return json_encode(['status'=>'error', 
														'code'=>'400', 
														'message'=> 'File already exist!', 
														'dest'=>$this->path['temp'].$filename]); // $destinationPath.$filename.' exist!'


			if(!is_dir($this->path['temp']))
				mkdir($this->path['temp'], 0775, true);

			$request->file('pic')->move($this->path['temp'], $filename);

			//$size = number_format(($request->file('pic')->getClientSize()/1000),0);
			$size = $this->web->fileSize(strtolower(session('user.branchcode')).DIRECTORY_SEPARATOR.now('year').DIRECTORY_SEPARATOR.$filename);

			$line = implode(' ', ['user:'.$request->user()->username, $filename.':'.$size.'KB']);
			$this->logAction('upload:success', $line);
			
		
			return json_encode(['status'=>'success', 
													'code'=>'200', 
													'message'=>'', 
													'year'=>$request->input('year'),
													'month'=>$request->input('month')]);
			*/	
			

		} else {
			return redirect('/upload/backup')->with('alert-error', 'File: '.$request->input('filename').' corrupted! Try to upload again..');
		}
		
	}

	
}