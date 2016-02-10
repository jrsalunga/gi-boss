<?php

namespace App\Providers;

use Auth;
use App\User;
use Illuminate\Support\ServiceProvider;

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
                $emp = User::with(['bossbranch'])->whereId(Auth::user()->id)->first();
                

                session(['user' => ['fullname'=>$emp->name, 
                        'id'=>$emp->id, 
                        'bossbranch'=>$emp->bossbranch, 
                        ]]);
            }
            
            
            $view->with('name', session('user.fullname'))->with('bossbranch', session('user.bossbranch'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
