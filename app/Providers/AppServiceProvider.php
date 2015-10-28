<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Auth;
use Illuminate\Http\Request;
use App\Models\Employee;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function($view){

            $id = empty(Auth::user()->id) ? '':Auth::user()->id;

            if(strtolower($id)!==strtolower(session('user.id'))){
                $emp = Employee::with(['branch'=>function($query){
                                $query->select('code', 'descriptor', 'mancost', 'id');
                            }])->where('id', Auth::user()->id)
                            ->get(['firstname', 'lastname', 'branchid', 'id'])->first();
                session(['user' => ['fullname'=>$emp->firstname.' '.$emp->lastname, 
                        'id'=>$emp->id, 'branchid'=>$emp->branchid, 
                        'branch'=>$emp->branch->descriptor, 
                        'branchcode'=>$emp->branch->code,
                        'branchmancost'=>$emp->branch->mancost]]);
            }
            
            $view->with('name', session('user.fullname'))->with('branch',  session('user.branch'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$
        $br = 'mar';
    }
}
