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
                                $query->select('code', 'addr1', 'id');
                            }])->where('id', Auth::user()->id)
                            ->get(['firstname', 'lastname', 'branchid', 'id'])->first();
                session(['user' => ['fullname'=>$emp->firstname.' '.$emp->lastname, 
                        'id'=>$emp->id, 'branchid'=>$emp->branchid, 
                        'branch'=>$emp->branch->addr1, 
                        'branchcode'=>$emp->branch->code]]);
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
