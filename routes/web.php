<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DurableGoodController;
use App\Http\Controllers\DurableGoodsImageController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ParcelController;
use App\Http\Controllers\ParcelImageController;
use App\Http\Controllers\ParcelStockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
Route::resource('user', UserController::class)->except(['show']);

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group(['middleware' => ['web', 'auth']], function () {

    // index
    // Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');

    // resource route
    Route::resource('user', UserController::class)->except(['show']);
    Route::resource('faculty', FacultyController::class)->except(['show']);
    Route::resource('department', DepartmentController::class)->except(['index', 'show', 'create']);
    Route::resource('location', LocationController::class)->except(['show']);
    Route::resource('durablegood', DurableGoodController::class)->except(['show']);
    Route::resource('durablegoodimage', DurableGoodsImageController::class)->except(['show']);
    Route::resource('parcel', ParcelController::class)->except(['show']);
    Route::resource('parcelimage', ParcelImageController::class)->except(['show']);
    Route::resource('parcelstock', ParcelStockController::class)->except(['index', 'show', 'create']);
    Route::resource('history', HistoryController::class)->except(['show']);
    Route::resource('report', ReportController::class)->except(['show']);
    Route::resource('dashboard', DashboardController::class);

    // datatable
    Route::get('/user/jsontable', 'App\Http\Controllers\UserController@jsontable')->name('user.jsontable');
    Route::get('/faculty/jsontable', 'App\Http\Controllers\FacultyController@jsontable')->name('faculty.jsontable');
    Route::get('/department/jsontable', 'App\Http\Controllers\DepartmentController@jsontable')->name('department.jsontable');
    Route::get('/location/jsontable', 'App\Http\Controllers\LocationController@jsontable')->name('location.jsontable');
    Route::get('/parcel/jsontable', 'App\Http\Controllers\ParcelController@jsontable')->name('parcel.jsontable');
    Route::get('/parcel/jsontable_withdraw', 'App\Http\Controllers\ParcelController@jsontable_withdraw')->name('parcel.jsontable_withdraw');
    Route::get('/parcelstock/jsontable', 'App\Http\Controllers\ParcelStockController@jsontable')->name('parcelstock.jsontable');
    Route::get('/durablegood/jsontable', 'App\Http\Controllers\DurableGoodController@jsontable')->name('durablegood.jsontable');
    Route::get('/durablegood/jsontable_withdraw', 'App\Http\Controllers\DurableGoodController@jsontable_withdraw')->name('durablegood.jsontable_withdraw');
    Route::get('/history/jsontable', 'App\Http\Controllers\HistoryController@jsontable')->name('history.jsontable');
    Route::get('/dashboard/jsontable', 'App\Http\Controllers\DashboardController@jsontable')->name('dashboard.jsontable');

    // other
    Route::get('/department/index/{facultyId}', 'App\Http\Controllers\DepartmentController@index')->name('department.index');
    Route::get('/department/create/{facultyId}', 'App\Http\Controllers\DepartmentController@create')->name('department.create');
    Route::get('/parcel/withdraw', 'App\Http\Controllers\ParcelController@withdraw')->name('parcel.withdraw');
    Route::get('/parcel/withdraw_form/{parcelId}', 'App\Http\Controllers\ParcelController@withdraw_form')->name('parcel.withdraw_form');
    Route::get('/durablegood/withdraw', 'App\Http\Controllers\DurableGoodController@withdraw')->name('durablegood.withdraw');
    Route::get('/history/parcel', 'App\Http\Controllers\HistoryController@parcel')->name('history.parcel');
    Route::get('/history/durablegood_approve', 'App\Http\Controllers\HistoryController@durablegood_approve')->name('history.durablegood_approve');
    Route::get('/history/durablegood_return', 'App\Http\Controllers\HistoryController@durablegood_return')->name('history.durablegood_return');
    Route::get('/parcelstock/index/{parcelId}', 'App\Http\Controllers\ParcelStockController@index')->name('parcelstock.index');
    Route::get('/parcelstock/create/{parcelId}', 'App\Http\Controllers\ParcelStockController@create')->name('parcelstock.create');
    Route::get('/report/export/{departmentId?}/{locationId?}/{statusId?}/{typeId?}/{startDate?}/{endDate?}', 'App\Http\Controllers\ReportController@export')->name('report.export');
    Route::get('/user/profile', 'App\Http\Controllers\UserController@profile')->name('user.profile');
    Route::get('/user/password/{userId}', 'App\Http\Controllers\UserController@password')->name('user.password');


});
