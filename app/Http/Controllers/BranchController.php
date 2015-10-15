<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Branch;

class BranchController extends Controller {





	public function getIndex() {
		return view('dashboard.index');
	}




	public function getBranchManager() {
		$postions = Position::all();
		$branches = Branch::all();
		return view('branch.manager')->with('postions', $postions)
																->with('branches', $branches);
	}

	public function postBranchManager(Request $request) {
		
		if(empty($request->input('g-recaptcha-response')))
			return redirect('branch/manage/user')->withErrors('please check/verify captcha!');

		return $request->all();
		//return $request->input('g-recaptcha-response');
	}


	
}