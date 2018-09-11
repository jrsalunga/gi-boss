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
Route::get('component/transfer', ['uses'=>'TransferController@getDaily']);
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



Route::get('branch', ['uses'=>'BranchController@getList']);

Route::resource('/masterfiles/lessor', 'LessorController');
Route::resource('/masterfiles/company', 'CompanyController');
Route::resource('/masterfiles/branch', 'BranchController');
Route::resource('/masterfiles/sector', 'SectorController');
Route::resource('/masterfiles/filetype', 'FiletypeController');
//Route::get('/masterfiles/branch/{id?}', ['uses'=>'BranchController@show2']);

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

Route::get('setslp/log', ['uses'=>'SetslpController@getHistory']);
Route::get('setslp/checklist', ['uses'=>'SetslpController@getChecklist']);
Route::get('setslp/{id?}/{action?}/{p?}', ['uses'=>'SetslpController@getAction']);
Route::get('images/setslp/{id?}', ['uses'=>'SetslpController@getImage']);
Route::put('put/setslp', ['uses'=>'SetslpController@put']);
Route::post('delete/setslp', ['uses'=>'SetslpController@delete']);

Route::get('timesheet/employee/{param1?}', ['as'=>'timesheet.daily', 'uses'=>'TimesheetController@employeeTimesheet']);
Route::get('timesheet/{param1?}', ['as'=>'timesheet.daily', 'uses'=>'TimesheetController@getRoute']);


Route::get('timelog/add', ['uses'=>'TimelogController@makeAddView']);
Route::delete('timelog/employee/{param1}', ['uses'=>'TimelogController@deleteEmployeeTimelog']);
Route::get('timelog/{param1?}/{param2?}', ['uses'=>'TimelogController@getRoute']);
Route::put('timelog/{id}/', ['uses'=>'TimelogController@put']);
Route::post('timelog', ['uses'=>'TimelogController@manualPost']);

Route::get('mansked/manday/{mandayid}', ['uses'=>'ManskedhdrController@getManday']);
Route::get('mansked/{param1?}', ['uses'=>'ManskedhdrController@getRoute']);


/******************* API  *************************************************/
Route::group(['prefix'=>'api'], function(){

Route::get('search/employee', ['uses'=>'EmployeeController@search']);
Route::get('t/purchase', ['uses'=>'PurchaseController@apiGetPurchase']);

});/******* end prefix:api ********/




/******************* API  *************************************************/
/******* end prefix:api ********/


}); /******* end middeware:auth ********/

Route::group(['prefix'=>'hr', 'middleware' => 'hr'], function(){

get('/', function () {
    return redirect('/hr/masterfiles/employee');
    return view('hr.index');
});
Route::get('masterfiles/employee/branch', 'Hr\BranchController@getBranch');

Route::get('masterfiles/{table?}', ['uses'=>'Hr\MasterfilesController@getIndex']);
Route::get('masterfiles/employee/{id}/edit/employment', 'Hr\EmployeeController@editEmployment');
Route::get('masterfiles/employee/{id}/edit/personal', 'Hr\EmployeeController@editPersonal');
Route::get('masterfiles/employee/{id}/edit/family', 'Hr\EmployeeController@editFamily');
Route::get('masterfiles/employee/{id}/edit/workedu', 'Hr\EmployeeController@editWorkedu');
Route::get('masterfiles/employee/{id}/edit/confirm', 'Hr\EmployeeController@editConfirm');
Route::delete('masterfiles/employee/child', 'Hr\EmployeeController@deleteChild');
Route::resource('masterfiles/employee', 'Hr\EmployeeController');
Route::get('masterfiles/employee/branch/{branchid?}', 'Hr\BranchController@branchEmployee');
Route::resource('masterfiles/position', 'Hr\PositionController');

});


get('mixmatch', function () {
    return view('mixmatch2');
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