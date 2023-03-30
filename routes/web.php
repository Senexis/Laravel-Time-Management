<?php

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

Auth::routes();
Route::post('logout', 'Auth\LoginController@logout')->name('auth.logout');

Route::group(['middleware' => ['auth', 'auth.check']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::post('/feedback', 'FeedbackController@store')->name('feedback.store');

    Route::get('/reports', 'ReportsController@index')->name('reports.index');
    Route::get('/reports/user', 'ReportsController@index')->name('reports.user');
    Route::get('/reports/project', 'ReportsController@project')->name('reports.project');
    Route::get('/reports/work-type', 'ReportsController@workType')->name('reports.work-type');

    Route::get('/search', 'SearchController@index')->name('search.index');

    Route::get('/time-entries/{time_entry}/lock', 'TimeEntriesController@lock')->name('time-entries.lock');
    Route::get('/time-entries/{time_entry}/unlock', 'TimeEntriesController@unlock')->name('time-entries.unlock');
    Route::post('/time-entries/batch-lock', 'TimeEntriesController@batchlock')->name('time-entries.batchlock');
    Route::post('/time-entries/batch-unlock', 'TimeEntriesController@batchunlock')->name('time-entries.batchunlock');
    Route::get('/time-entries/{time_entry}/pause', 'TimeEntriesController@pause')->name('time-entries.pause');
    Route::get('/time-entries/{time_entry}/resume', 'TimeEntriesController@resume')->name('time-entries.resume');
    Route::get('/time-entries/{time_entry}/stop', 'TimeEntriesController@stop')->name('time-entries.stop');
    Route::get('/time-entries/export-csv', 'TimeEntriesController@exportCsv')->name('time-entries.export-csv');

    Route::get('/users/{user}/login-as', 'UsersController@loginAs')->name('users.login-as');

    Route::resources([
        'projects' => 'ProjectsController',
        'roles' => 'RolesController',
        'time-entries' => 'TimeEntriesController',
        'user-actions' => 'UserActionsController',
        'user-locations' => 'UserLocationController',
        'users' => 'UsersController',
        'work-types' => 'WorkTypesController',
    ]);
});
