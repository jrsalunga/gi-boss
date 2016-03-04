<?php






Route::get('login', ['as'=>'auth.getlogin', 'uses'=>'Auth\AuthController@getLogin']);
Route::post('login', ['as'=>'auth.postlogin', 'uses'=>'Auth\AuthController@postLogin']);
Route::get('logout', ['as'=>'auth.getlogout', 'uses'=>'Auth\AuthController@getLogout']);


Route::group(['middleware' => 'auth'], function(){

Route::get('/', ['uses'=>'DashboardController@getIndex']);

Route::get('settings/{param1?}/{param2?}', ['uses'=>'SettingsController@getIndex'])
    ->where(['param1'=>'password|bossbranch', 
                    'param2'=>'week|[0-9]+']);

Route::post('/settings/password',  ['uses'=>'SettingsController@changePassword']);
Route::post('/settings/bossbranch',  ['uses'=>'SettingsController@assignBranch']);

Route::get('/backup',  ['uses'=>'BackupController@index']);
Route::get('/storage/log',  ['uses'=>'BackupController@getHistory']);
Route::get('/backup/delinquent',  ['uses'=>'BackupController@getDelinquent']);
Route::get('/show/delinquent',  ['uses'=>'BackupController@delinquent']);
Route::get('/storage/{param1?}/{param2?}/{param3?}',  ['uses'=>'BackupController@getStorage']);
Route::get('download/{param1?}/{param2?}/{param3?}/{param4?}', ['uses'=>'BackupController@getDownload']);

Route::get('dashboard', ['uses'=>'DashboardController@getIndex']);
Route::get('sales', ['uses'=>'DashboardController@getSales']);
Route::get('dailysales', ['uses'=>'DashboardController@getDailySales']);
Route::get('dailysales/all', ['uses'=>'DashboardController@getDailySalesAll']);
Route::get('api/tsv', ['uses'=>'DashboardController@getDashboardTSV']);
Route::get('api/csv', ['uses'=>'DashboardController@getDashboardCSV']);



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