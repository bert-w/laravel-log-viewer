<?php

use Illuminate\Support\Facades\Route;

Route::get('/', ['uses' => 'LogViewerController@index', 'as' => 'logviewer.index']);
Route::get('/{file}/download', ['uses' => 'LogViewerController@download', 'as' => 'logviewer.download']);
Route::get('/{file}/raw', ['uses' => 'LogViewerController@raw', 'as' => 'logviewer.raw']);
Route::delete('/{file}', ['uses' => 'LogViewerController@destroy', 'as' => 'logviewer.destroy']);
