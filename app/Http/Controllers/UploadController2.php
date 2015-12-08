<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Input;
use File;
use Storage;

class UploadController2 extends Controller {

	protected $fs;
	protected $path = [];

	public function __construct(Filesystem $filesystem){
		$this->fs = $filesystem;
		//$this->path = config('gi-dtr.upload_path');
		$this->path['temp'] = config('gi-dtr.upload_path')['temp'].session('user.branchcode').DIRECTORY_SEPARATOR.now('year').DIRECTORY_SEPARATOR;
		$this->path['storage'] = config('gi-dtr.upload_path.pos')[app()->environment()].session('user.branchcode').DIRECTORY_SEPARATOR.now('year').DIRECTORY_SEPARATOR;
	}


	public function getBackup(Request $request){
		return view('upload.backup');
	}

	public function index() {
		return view('upload.index');
	} 

	public function putfile(Request $request) {
	
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

	public function postfile(Request $request) {
		//$request->file('pic');
		//$request->file('photo')->move($destinationPath);
		$filename = $request->file('pic')->getClientOriginalName();
		
		//$fs = new Filesystem;
		if($this->fs->exists($this->path['temp'].$filename)){
			return json_encode(['status'=>'error', 'code'=>'400', 'message'=> 'File already exist!', 'dest'=>$this->path['temp'].$filename]); // $destinationPath.$filename.' exist!'
		} else {


			if(!is_dir($this->path['temp']))
				mkdir($this->path['temp'], 0775, true);

			$request->file('pic')->move($this->path['temp'], $filename);

			$size = number_format(($request->file('pic')->getClientSize()/1000),0);

			$line = implode(' ', ['user:'.$request->user()->username, $filename.':'.$size.'KB']);
			$this->logAction('upload:success', $line);
		
			return json_encode(['status'=>'success', 'code'=>'200']);
			
		}

		

		
		
	}

	
}