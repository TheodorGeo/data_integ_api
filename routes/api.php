<?php

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


/*
|--------------------------------------------------------------------------
|Registered routes for JIRA (software) cloud
|--------------------------------------------------------------------------
*/

//Route to get all projects basic infos via user's token, domain and email
Route::get('/jira/projects', 'JiraController@index')->middleware('cors');

//Route to get a project infos via users' token domain email and project key
Route::get('/jira/projects/{projectKey}', 'JiraController@project')->middleware('cors');

//Route to get a project's issues and associated users via users' token domain email and project key
Route::get('/jira/project/{projectKey}', 'JiraController@show')->middleware('cors');


/*
|--------------------------------------------------------------------------
|Registered routes for Wrike
|--------------------------------------------------------------------------
*/

//Route to get all projects and folders basic infos via user's token
Route::get('/wrike/folders', 'WrikeController@index')->middleware('cors');

//Route to get all tasks inside a folder or a project via folders id
Route::get('/wrike/folder/{id}', 'WrikeController@showById')->middleware('cors');

//Route to get all tasks inside a folder or a project via folders name/title
Route::get('/wrike/folder/', 'WrikeController@showByName')->middleware('cors');

//Route to get all users/assignees of a project/folders
Route::get('/wrike/folder/{id}/users', 'WrikeController@users')->middleware('cors');


/*
|--------------------------------------------------------------------------
|Registered routes for Asana
|--------------------------------------------------------------------------
*/

//Route to get all projects via user's token
Route::get('/asana/projects', 'AsanaController@index')->middleware('cors');

//Route to get all tasks or users inside a project
Route::get('/asana/project/{id}', 'AsanaController@show')->middleware('cors');

//Route to get all tasks in a project
Route::get('/asana/project/{id}/tasks', 'AsanaController@tasks')->middleware('cors');

//Route to get all users in a project's Workspace
Route::get('/asana/project/{id}/users', 'AsanaController@users')->middleware('cors');

//// TODO: Create routes for mapping data from 4 PM tools to one integrated Schema
Route::post('/integration', 'IntegrationController@index')->middleware('cors');