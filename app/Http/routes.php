<?php


Route::get('login', ['as'=>'auth.getlogin', 'uses'=>'Auth\AuthController@getLogin']);
Route::post('login', ['as'=>'auth.postlogin', 'uses'=>'Auth\AuthController@postLogin']);
Route::get('logout', ['as'=>'auth.getlogout', 'uses'=>'Auth\AuthController@getLogout']);


Route::group(['middleware' => 'auth'], function(){

Route::get('/', ['uses'=>'DashboardController@getIndex']);

Route::get('settings/{param1?}/{param2?}', ['uses'=>'SettingsController@getIndex'])
    ->where(['param1'=>'password|bossbranch', 'param2'=>'week|[0-9]+']);

Route::post('/settings/password',  ['uses'=>'SettingsController@changePassword']);
Route::post('/settings/bossbranch',  ['uses'=>'SettingsController@assignBranch']);
Route::get('/settings/emp-import',  ['uses'=>'SettingsController@empImport']);
Route::post('/settings/emp-import',  ['uses'=>'SettingsController@postEmpImport']);

Route::get('/backup',  ['uses'=>'BackupController@index']);
Route::get('backup/checklist', ['uses'=>'BackupController@getChecklist']);
Route::get('/storage/log',  ['uses'=>'BackupController@getHistory']);
Route::get('/storage/batch-download',  ['uses'=>'BackupController@getBatchDownload']);
Route::post('/storage/batch-download',  ['uses'=>'BackupController@postBatchDownload']);
Route::get('/backup/delinquent',  ['uses'=>'BackupController@getDelinquent']);
Route::get('/show/delinquent',  ['uses'=>'BackupController@delinquent']);
Route::get('/storage/{param1?}/{param2?}/{param3?}',  ['uses'=>'BackupController@getStorage']);

/*
Route::get('status/branch/{branchid?}', ['uses'=>'BranchController@getStatus'])
    ->where(['branchid'=>'[0-9a-zA-z]{32}+']);
Route::post('status/branch', ['uses'=>'BranchController@postStatus']);
*/
Route::post('status/post-comparative', ['uses'=>'BranchController@postComparative']);
Route::get('status/comparative', ['uses'=>'BranchController@getComparative']);
Route::get('status/branch', ['uses'=>'AnalyticsController@getDaily']);
Route::get('status/branch/month', ['uses'=>'AnalyticsController@getMonth']);
Route::get('status/branch/week', ['uses'=>'AnalyticsController@getWeekly']);
Route::get('status/branch/quarter', ['uses'=>'AnalyticsController@getQuarter']);
Route::get('status/branch/year', ['uses'=>'AnalyticsController@getYear']);

Route::get('pnl/branch/month', ['uses'=>'PnlController@getMonth']);

Route::get('report/comp-purch', ['uses'=>'AnalyticsController@getCompPurch']);


//Route::get('component', ['uses'=>'Purchase2Controller@getIndex']);
Route::get('component/purchases', ['uses'=>'Purchase2Controller@getDaily']);
Route::get('component/price/comparative', ['uses'=>'Purchase2Controller@componentComparative']);
Route::get('api/search/component', ['uses'=>'Purchase2Controller@search']);
Route::get('api/s/product/sales', ['uses'=>'SaleController@search']);
Route::get('api/mdl/purchases/{id}', ['uses'=>'Purchase2Controller@ajaxPurchases']);

Route::get('product/sales', ['uses'=>'SaleController@getDaily']);
Route::get('product/sales/comparative', ['uses'=>'SaleController@productComparative']);

Route::get('m/{table?}', ['uses'=>'MasterfilesController@getDatatableIndex']);
Route::get('masterfiles/{table?}', ['uses'=>'MasterfilesController@getIndex']);
Route::get('api/m/{table?}', ['uses'=>'MasterfilesController@getController']);
Route::get('api/getdt', ['uses'=>'MasterfilesController@getDatatablesData']);
Route::get('api/mdl/sales/{id}', ['uses'=>'SaleController@ajaxSales']);

Route::get('dashboard', ['uses'=>'DashboardController@getIndex']);
Route::get('sales', ['uses'=>'DashboardController@getSales']);
Route::get('dailysales', ['uses'=>'DashboardController@getDailySales']);
Route::get('dailysales/all', ['uses'=>'DashboardController@getDailySalesAll']);
Route::get('api/tsv', ['uses'=>'DashboardController@getDashboardTSV']);
Route::get('api/csv', ['uses'=>'DashboardController@getDashboardCSV']);

Route::get('employee/watchlist', ['uses'=>'EmployeeController@getWatchlist']);
Route::get('employee/watchlist/summary', ['uses'=>'EmployeeController@getWatchlistSummary']);
Route::get('employee/tracker', ['uses'=>'EmployeeController@getWatchlist']);
Route::get('employee/tracker/summary', ['uses'=>'EmployeeController@getWatchlistSummary']);

Route::post('api/csv/comparative', ['uses'=>'BranchController@getComparativeCSV']);
Route::post('api/json/comparative', ['uses'=>'BranchController@getComparativeJSON']);

/** Depslip **/
Route::get('depslp/log', ['uses'=>'DepslpController@getHistory']);
Route::get('depslp/checklist', ['uses'=>'DepslpController@getChecklist2']);
Route::get('depslp/{id?}/{action?}/{p?}', ['uses'=>'DepslpController@getAction']);
Route::get('images/depslp/{id?}', ['uses'=>'DepslpController@getImage']);
Route::put('put/depslp', ['uses'=>'DepslpController@put']);
Route::post('delete/depslp', ['uses'=>'DepslpController@delete']);
Route::get('download/DEPSLP/{param1?}/{param2?}/{param3?}/{param4?}', ['uses'=>'DepslpController@getDownload']);
Route::get('download/{param1?}/{param2?}/{param3?}/{param4?}', ['uses'=>'BackupController@getDownload']);


Route::get('timesheet/employee/{param1?}', ['as'=>'timesheet.daily', 'uses'=>'TimesheetController@employeeTimesheet']);
Route::get('timesheet/{param1?}', ['as'=>'timesheet.daily', 'uses'=>'TimesheetController@getRoute']);

Route::delete('timelog/employee/{param1}', ['uses'=>'TimelogController@deleteEmployeeTimelog']);
Route::get('timelog/{param1?}/{param2?}', ['uses'=>'TimelogController@getRoute']);
Route::put('timelog/{id}/', ['uses'=>'TimelogController@put']);

Route::get('mansked/manday/{mandayid}', ['uses'=>'ManskedhdrController@getManday']);
Route::get('mansked/{param1?}', ['uses'=>'ManskedhdrController@getRoute']);


/******************* API  *************************************************/
Route::group(['prefix'=>'api'], function(){

Route::get('t/purchase', ['uses'=>'PurchaseController@apiGetPurchase']);

});/******* end prefix:api ********/


}); /******* end middeware:auth ********/


get('branch', function () {
    return App\User::with(['bossbranch'=>function($query){
    	$query->select('bossid', 'branchid', 'id')
    	->with(['branch'=>function($query){
    		$query->select('code', 'descriptor', 'id');
    	}]);
    }])->get();
});


get('getweek', function () {

    return range(14, 17);


    $arr = [];

    for ($i=2008; $i < 2021; $i++) { 
        $date = Carbon\Carbon::parse($i.'-08-27');
        array_push($arr, [
            'year' => $i,
            'day'   => $date->endOfYear()->format('Y-m-d D'),
            'week' => $date->endOfYear()->weekOfYear,
            'wday' => $date->endOfYear()->dayOfWeek,
            'lwoy' => lastWeekOfYear($i)
        ]);
    }


    return $arr;

    


});















get('sessions', function(){
	return session()->all();
});