<?php

use Illuminate\Http\Request;



/*
|--------------------------------------------------------------------------
|Registered routes for Trello
|--------------------------------------------------------------------------
*/

//Route to get all boards basic infos via user's token (id,name, shortlink)
Route::get('/trello/boards','TrelloController@index')->middleware('cors');

//Route to get a board's id and name via it's shortLink
Route::get('/trello/boards/id/{shortLink}', 'TrelloController@shortLink')->middleware('cors');

//Route to get a board's infos (cards,lists,checklists etc) via it's id
Route::get('/trello/board/id/{id}', 'TrelloController@boardById')->middleware('cors');

//Route to get a board's infos (cards,lists,checklists etc) via it's shortLink
Route::get('/trello/board/shortlink/{shortLink}', 'TrelloController@boardByShortLink')->middleware('cors');
