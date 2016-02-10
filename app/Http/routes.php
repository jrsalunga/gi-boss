<?php






Route::get('login', ['as'=>'auth.getlogin', 'uses'=>'Auth\AuthController@getLogin']);
Route::post('login', ['as'=>'auth.postlogin', 'uses'=>'Auth\AuthController@postLogin']);
Route::get('logout', ['as'=>'auth.getlogout', 'uses'=>'Auth\AuthController@getLogout']);


Route::group(['middleware' => 'auth'], function(){

Route::get('settings/{param1?}/{param2?}', ['uses'=>'SettingsController@getIndex'])
    ->where(['param1'=>'password', 
                    'param2'=>'week|[0-9]+']);

Route::post('/settings/password',  ['uses'=>'SettingsController@changePassword']);


Route::get('/', ['uses'=>'DashboardController@getIndex']);

Route::get('dashboard', ['uses'=>'DashboardController@getIndex']);


}); /******* end middeware:auth ********/



get('branch', function () {
    return App\User::with(['bossbranch'=>function($query){
    	$query->select('bossid', 'branchid', 'id')
    	->with(['branch'=>function($query){
    		$query->select('code', 'descriptor', 'id');
    	}]);
    }])->get();

    


});












get('sessions', function(){
	return session()->all();
});