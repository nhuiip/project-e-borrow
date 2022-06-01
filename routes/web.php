<?php

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
    Route::resource('parcelstock', ParcelStockController::class)->except(['show']);
    Route::resource('history', HistoryController::class)->except(['show']);
    Route::resource('report', ReportController::class)->except(['show']);

    // datatable
    Route::get('/user/jsontable', 'App\Http\Controllers\UserController@jsontable')->name('user.jsontable');
    Route::get('/faculty/jsontable', 'App\Http\Controllers\FacultyController@jsontable')->name('faculty.jsontable');
    Route::get('/department/jsontable', 'App\Http\Controllers\DepartmentController@jsontable')->name('department.jsontable');
    Route::get('/location/jsontable', 'App\Http\Controllers\LocationController@jsontable')->name('location.jsontable');

    // other
    Route::get('/department/index/{facultyId}', 'App\Http\Controllers\DepartmentController@index')->name('department.index');
    Route::get('/department/create/{facultyId}', 'App\Http\Controllers\DepartmentController@create')->name('department.create');
    Route::get('/department/getdepartment', 'App\Http\Controllers\DepartmentController@getdepartment')->name('department.getdepartment');
    Route::put('/user/password/{userId}', 'App\Http\Controllers\UserController@password')->name('user.password');


});
