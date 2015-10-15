<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use \Input;
use \File;

class UploadController extends Controller {





	public function index() {
		return view('upload.index');
	} 



	public function postfile(Request $request) {

		//$request->file('pic');
		//$request->file('photo')->move($destinationPath);
		$filename = Input::file('pic')->getClientOriginalName();
		
		if(app()->environment()=='local') {
			$destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
		} else {
			$destinationPath = '/home/server-admin/Public/maindepot/';
		}
		
		$fs = new Filesystem;
		if($fs->exists($destinationPath.$filename)){
			return json_encode(['error'=>'404', 'message'=> $destinationPath.$filename.' exist!']);
		} else {
			Input::file('pic')->move($destinationPath, $filename);
		}
		return json_encode(['success'=>'200']);

		$demo_mode = true;
		$upload_dir = public_path().'/uploads/';
		//$upload_dir = 'uploads/';
		$allowed_ext = array('jpg','jpeg','png','gif', 'zip');
		//if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
		//	exit_status('Error! Wrong HTTP method!');
		//}
		echo var_dump($_FILES['pic']);
		if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){
			$pic = $_FILES['pic'];
			
			
			if(!in_array($this->get_extension($pic['name']),$allowed_ext)){
				$this->exit_status('Only '.implode(',',$allowed_ext).' files are allowed!');
			}	
			if($demo_mode){
				// File uploads are ignored. We only log them.
				$line = implode('		', array( date('r'), $_SERVER['REMOTE_ADDR'], $pic['size'], $pic['name']));
				file_put_contents('../logs/image-upload-log.txt', $line.PHP_EOL, FILE_APPEND);
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