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

Route::get('teamhunt/{hunt}', 'TeamHuntController@find');
Route::get('teamhunt/{hunt}/items', 'TeamHuntController@items');
Route::post('teamhunt/calculate', 'TeamHuntController@calculate');
Route::post('teamhunt/{hunt}/item/{item}', 'TeamHuntController@updateItem');
Route::post('teamhunt/{hunt}/teammate/{teammate}', 'TeamHuntController@updateTeammate');
Route::post('teamhunt/{hunt}/sign', 'TeamHuntController@signPassword');

Route::get('tiles', 'TileController@index');

Route::get('imbuements', 'ImbuementController@index');

Route::post('contact', 'PageController@sendContact');

//Route::get('mvp/{mvp}', 'MVPController@show');
//Route::post('mvp', 'MVPController@calculate');

/**
 * CreatureController routes.
 */
Route::get('creatures', 'CreatureController@index');
Route::get('creatures/multiple', 'CreatureController@multiple');
Route::get('creatures/{creature}', 'CreatureController@show');

/**
 * HuntingSpotsController routes.
 */
Route::get('hunting-spots', 'HuntingSpotController@index');
Route::get('hunting-spots/categories', 'HuntingSpotController@categories');
Route::get('hunting-spots/{spot}', 'HuntingSpotController@show');
Route::get('supplies', 'HuntingSpotController@supplies');
Route::get('equipments', 'HuntingSpotController@equipments');
Route::post('hunting-spots', 'HuntingSpotController@store');

/**
 * Authentication routes.
 */
Route::get('auth/token', 'AuthController@refreshToken');
Route::post('auth', 'AuthController@authenticate');


/**
 * PostController routes
 */
Route::get('news', 'PostController@news');
Route::get('news/hot', 'PostController@hotnews');
Route::get('news/show', 'PostController@show');

/**
 * ChangeController routes
 */
Route::get('change-log', 'ChangeController@getChanges');

/**
 * CategoryController
 */
Route::get('categories', 'CategoryController@usables');

/**
 * ItemController
 */
Route::get('items/{category}', 'ItemController@usables');

/**
 * WorldController routes
 */
Route::get('worlds', 'WorldController@index');
Route::get('worlds/{world}', 'WorldController@show');
Route::get('worlds/{world}/currencies', 'WorldController@currencies');

/**
 * VocationController routes.
 */
Route::get('vocations', 'VocationController@index');

/**
 * QuickLootingController routes.
 */
Route::get('quick-looting/creatures', 'QuickLootingController@creatures');
Route::get('quick-looting/categories', 'QuickLootingController@categories');
Route::get('quick-looting/items', 'QuickLootingController@items');

/**
 * PartnersController
 */
Route::get('partners', 'PartnersController@index');

/*
 * HighscoresController routes.
 */
Route::get('highscores', 'HighscoresController@experience');
Route::get('highscores/skills/{type?}', 'HighscoresController@skills');

/**
 * PlayerController routes.
 */
Route::get('players/{name}', 'PlayersController@show');

/**
 * All the routes in this group will need to send a Header
 * Authorization with a valide token, withou this the user will
 * not be authorized to access the route.
 */
Route::group(['middleware' => 'auth:api', 'prefix' => 'admin'], function () {
    
    /**
     * AuthController routes.
     */
    Route::post('auth/user', 'AuthController@getAuthenticatedUser');

    /**
     * CategoryController routes.
     */
    Route::get('categories', 'CategoryController@index');
    Route::get('categories/{category}/show', 'CategoryController@show');
    Route::get('categories/{category}/usable', 'CategoryController@toggleUsable');
    Route::patch('categories/{category}', 'CategoryController@update');
    Route::post('categories/sync', 'CategoryController@syncronize');

    /**
     * ItemController routes.
     */
    Route::get('items/{category}', 'ItemController@index');
    Route::get('items/{item}/show', 'ItemController@show');
    Route::get('items/{item}/usable', 'ItemController@toggleUsable');
    Route::post('items/{item}/property', 'ItemController@updateProperty');
    Route::post('items/sync', 'ItemController@syncronize');
    Route::delete('items/{item}', 'ItemController@destroy');

    /**
     * ItemController routes.
     */
    Route::post('npcs/sync', 'NPCController@syncronize');

    /**
     * CreatureController routes.
     */
    Route::post('creatures/sync', 'CreatureController@syncronize');

    /**
     * TileController routes.
     */
    Route::post('tiles/sync', 'TileController@syncronize');

    /**
     * WorldMapController routes.
     */
    Route::post('map/sync', 'WorldMapController@syncronize');

    /**
     * TranslationController routes.
     */
    Route::get('translations', 'TranslationController@index');
    Route::post('translations', 'TranslationController@store');
    Route::patch('translations/{translation}', 'TranslationController@update');
    Route::delete('translations/{translation}', 'TranslationController@destroy');

    /**
     * NewsController routes
     */
    Route::get('posts', 'PostController@index');
    Route::get('posts/{post}', 'PostController@show');
    Route::post('posts', 'PostController@store');
    Route::patch('posts/{post}', 'PostController@update');
    Route::delete('posts/{post}', 'PostController@destroy');

    /**
     * ChangeController routes
     */
    Route::get('changes', 'ChangeController@index');
    Route::get('changes/{change}', 'ChangeController@show');
    Route::post('changes', 'ChangeController@store');
    Route::patch('changes/{change}', 'ChangeController@update');
    Route::delete('changes/{change}', 'ChangeController@destroy');

    /**
     * ImbuementController routes.
     */
    Route::get('imbuements', 'ImbuementController@index');
    Route::get('imbuements/{imbuement}', 'ImbuementController@show');
    Route::post('imbuements', 'ImbuementController@store');
    Route::patch('imbuements/{imbuement}', 'ImbuementController@update');

    /**
     * WorldController routes.
     */
    Route::get('worlds', 'WorldController@index');
    Route::get('worlds/{world}', 'WorldController@show');
    Route::post('worlds', 'WorldController@store');
    Route::patch('worlds/{world}', 'WorldController@update');

    /**
     * WorldCurrencyController
     */
    Route::post('worlds/{world}/currencies', 'WorldCurrencyController@store');
    Route::patch('worlds/currencies/{currency}', 'WorldCurrencyController@update');
    Route::delete('worlds/currencies/{currency}', 'WorldCurrencyController@destroy');

    /**
     * PartnersController routes.
     */
    Route::get('partners', 'PartnersController@index');
    Route::get('partners/{partner}', 'PartnersController@show');
    Route::post('partners', 'PartnersController@store');
    Route::patch('partners/{partner}', 'PartnersController@update');
    Route::delete('partners/{partner}', 'PartnersController@destroy');
});
