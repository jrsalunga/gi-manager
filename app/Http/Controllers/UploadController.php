<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use \Input;
use \File;
use Storage;

class UploadController extends Controller {

	protected $fs;
	protected $path = [];

	public function __construct(){
		$this->fs = new Filesystem;
		//$this->path = config('gi-dtr.upload_path');
		$this->path['temp'] = config('gi-dtr.upload_path')['temp'].now('year').DIRECTORY_SEPARATOR.session('user.branchcode').DIRECTORY_SEPARATOR;
		$this->path[app()->environment()] = config('gi-dtr.upload_path')[app()->environment()].now('year').DIRECTORY_SEPARATOR.session('user.branchcode').DIRECTORY_SEPARATOR;
	}


	public function getBackup(Request $request){
		return view('upload.backup');
	}

	public function index() {
		return view('upload.index');
	} 

	public function putfile(Request $request) {
	
		if($this->fs->exists($this->path['temp'].$request->input('filename'))){
			if($this->fs->exists($this->path[app()->environment()].$request->input('filename'))){ 
				$this->logAction('move:error', $request->input('filename').' message:file_exist');
				return redirect('/upload/backup')->with('alert-error', 'File: '.$this->path[app()->environment()].$request->input('filename').' exist!');
			} else {

				if(!is_dir($this->path[app()->environment()]))
					mkdir($this->path[app()->environment()], 0755, true);

				try {
					File::move($this->path['temp'].$request->input('filename'), $this->path[app()->environment()].$request->input('filename'));
				}catch(\Exception $e){
					$this->logAction('move:error', $request->input('filename').' message:'.$e->getMessage());
					return redirect('/upload/backup')->with('alert-error', $e->getMessage());
				}

				$this->logAction('move:success', $request->input('filename'));
				return redirect('/upload/backup')->with('alert-success', 'File: '.$request->input('filename').' successfully uploaded!');
			}
		} else {
			$this->logAction('move:error', $request->input('filename').' message:try_again');
			return redirect('/upload/backup')->with('alert-error', 'File: '.$request->input('filename').' do not exist! Try to upload again..');
		}
	} 


	private function logAction($action, $log) {
		$logfile = base_path().DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.now().'-log.txt';
		$new = file_exists($logfile) ? false : true;
		if($new){
			$handle = fopen($logfile, 'w');
			chmod($logfile, 0755);
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

		

		

		$demo_mode = true;
		$upload_dir = public_path().'/uploads/';
		//$upload_dir = 'uploads/';
		$allowed_ext = array('jpg','jpeg','png','gif', 'zip');
		//if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
		//	exit_status('Error! Wrong HTTP method!');
		//}
		//echo var_dump($_FILES['pic']);
		if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){
			$pic = $_FILES['pic'];
			
			
			if(!in_array($this->get_extension($pic['name']),$allowed_ext)){
				$this->exit_status('Only '.implode(',',$allowed_ext).' files are allowed!');
			}	
			if($demo_mode){
				// File uploads are ignored. We only log them.
				$line = implode(' ', array( date('r'), $_SERVER['REMOTE_ADDR'], $pic['size'], $pic['name']));
				file_put_contents(base_path().'/logs/image-upload-log.txt', $line.PHP_EOL, FILE_APPEND);
				$this->exit_status('Uploads are ignored in demo mode.');
			}
			// Move the uploaded file from the temporary
			// directory to the uploads folder:
			if(move_uploaded_file($pic['tmp_name'], $upload_dir.$pic['name'])){
				$this->exit_status('File was uploaded successfuly!');
			}
		}
		$this->exit_status('Something went wrong with your upload!');
		// Helper functions
		
	}

	public function exit_status($str){
			echo json_encode(array('status'=>$str));
			exit;
		}
		public function get_extension($file_name){
			$ext = explode('.', $file_name);
			$ext = array_pop($ext);
			return strtolower($ext);
		}
}