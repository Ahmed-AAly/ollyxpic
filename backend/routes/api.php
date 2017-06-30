<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('items', 'ItemsController@index');
Route::get('items/potions', 'ItemsController@potions');
Route::get('items/ammunitions', 'ItemsController@ammunitions');
Route::get('items/runes', 'ItemsController@runes');
Route::get('items/amulets', 'ItemsController@amulets');
Route::get('items/rings', 'ItemsController@rings');

Route::post('waste/calculate', 'WasteController@calculate');
Route::get('waste/{waste}', 'WasteController@find');

Route::post('teamhunt/calculate', 'TeamHuntController@calculate');
Route::get('teamhunt/{hunt}', 'TeamHuntController@find');
Route::post('teamhunt/{hunt}/item/{item}', 'TeamHuntController@updateItem');
Route::post('teamhunt/{hunt}/teammate/{teammate}', 'TeamHuntController@updateTeammate');
Route::post('teamhunt/{hunt}/sign', 'TeamHuntController@signPassword');

//Route::get('npcs', 'NPCController@index');
