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

//Route to get all boards basic infos via user's token (id,name, shortlink)
Route::get('/trello/boards','TrelloController@index');

//Route to get a board's id and name via it's shortLink
Route::get('/trello/boards/id/{shortLink}', 'TrelloController@shortLink');
