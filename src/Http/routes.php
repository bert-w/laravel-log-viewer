<?php

use Illuminate\Support\Facades\Route;

Route::get('/', ['uses' => 'LogViewerController@index', 'as' => 'index']);
Route::get('/{file}/download', ['uses' => 'LogViewerController@download', 'as' => 'download']);
Route::get('/{file}/raw', ['uses' => 'LogViewerController@raw', 'as' => 'raw']);
Route::delete('/{file}', ['uses' => 'LogViewerController@destroy', 'as' => 'destroy']);
