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

        view()->composer(['menu.main', 'menu.sub', '_partials.pager'], 'App\Http\ViewComposers\MainMenuComposer');
        view()->composer(['menu.main-hr', 'menu.sub-hr', '_partials.pager-hr'], 'App\Http\ViewComposers\HrMainMenuComposer');
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
