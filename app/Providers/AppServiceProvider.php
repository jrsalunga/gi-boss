<?php

namespace App\Providers;

use Auth;
use App\User;
use Validator;
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

        view()->composer(['menu.main', 'menu.sub', '_partials.pager'], 'App\Http\ViewComposers\MainMenuComposer');
        view()->composer(['menu.main-hr', 'menu.sub-hr', '_partials.hr-pager'], 'App\Http\ViewComposers\HrMainMenuComposer');


        Validator::extend('alpha_spaces', function ($attribute, $value) {
            // This will only accept alpha and spaces. 
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            //return preg_match('/^[\pL\s]+$/u', $value); 
            return preg_match('/^[\pL\s-]+$/u', $value); 
        });

        Validator::extend('anshup', function ($attribute, $value) {
            return preg_match('/^[0-9\pL\s-_.]+$/u', $value); 
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
