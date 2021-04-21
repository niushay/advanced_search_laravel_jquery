<?php

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


use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SearchController;


///////////show list of the queries in the search bar
Route::get('/', 'SearchController@index')->name('index');
Route::post('/search', 'SearchController@search')->name('search');



//////////show list of the queries in the list
Route::get('/list','SearchController@index_list')->name('list');
Route::post('/edit', 'SearchController@edit')->name('edit');


//////////get all data at first
Route::get('/totaldata', 'SearchController@totaldata')->name('totaldata');

