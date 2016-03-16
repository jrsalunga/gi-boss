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


Route::get('status/branch/{branchid?}', ['uses'=>'BranchController@getStatus'])
    ->where(['branchid'=>'[0-9a-zA-z]{32}+']);
Route::post('status/branch', ['uses'=>'BranchController@postStatus']);

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


get('dailysales/recompute', function () {
    //$dss = App\Models\DailySales::all();
    //$dss = App\Models\DailySales::take(10)->get();

    foreach ($dss as $ds) {
        
         
        $headspend  = $ds->custcount=='0' ? 0:($ds->sales/$ds->custcount);
        $tipspct    = ($ds->sales=='0.00' || $ds->sales=='0') ? 0 : (($ds->tips/$ds->sales)*100);
        $mancostpct = ($ds->sales=='0.00' || $ds->sales=='0') ? 0 : ((650*$ds->empcount)/$ds->sales)*100;
        $cospct = 0;

        if(is_null($ds->headspend) || empty($ds->headspend)) {

            $ds->headspend  = number_format($headspend, 2);
            $ds->tipspct    = number_format($tipspct, 2);
            $ds->mancostpct = number_format($mancostpct, 2);
            $ds->cospct     =  number_format($cospct, 2);
            $ds->save();

            echo number_format($headspend, 2).' - ';
            echo number_format($tipspct, 2).' - ';
            echo number_format($mancostpct, 2).' - ';
            echo number_format($cospct, 2).'<br>';
        }
    }


});












get('sessions', function(){
	return session()->all();
});