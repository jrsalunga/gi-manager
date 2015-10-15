<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class DashboardController extends Controller {





	public function getIndex(){
		return view('dashboard.index');
	}
}