<?php

namespace App\Providers;

use Event;
use Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use App\User;
use App\Models\Manskedhdr as Mansked;
use App\Events\ManskedhdrCreated as ManskedCreated;
use App\Events\ManskedhdrUpdated as ManskedUpdated;
use App\Events\ManskedhdrDeleted as ManskedDeleted;

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
                $emp = User::with(['branch'=>function($query){
                                $query->select('code', 'descriptor', 'mancost', 'id');
                            }])->where('id', Auth::user()->id)
                            ->get(['name', 'branchid', 'id'])->first();
                session(['user' => ['fullname'=>$emp->name, 
                        'id'=>$emp->id, 'branchid'=>$emp->branchid, 
                        'branch'=>$emp->branch->descriptor, 
                        'branchcode'=>$emp->branch->code,
                        'branchmancost'=>$emp->branch->mancost]]);
            }
            
            
            $view->with('name', session('user.fullname'))->with('branch',  session('user.branch'));
        });
        


        Mansked::created(function ($mansked) {
            Event::fire(new ManskedCreated($mansked));
        });

        Mansked::updated(function ($mansked) {
           event(new ManskedUpdated($mansked)); // using the global event 
        });

        Mansked::deleted(function ($mansked) {
            Event::fire(new ManskedDeleted($mansked));
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
        //$br = 'mar';
    }
}
