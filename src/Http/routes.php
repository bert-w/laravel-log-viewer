<?php

use Illuminate\Support\Facades\Route;

Route::get('/{logViewerFile}/download', ['uses' => 'LogViewerController@download', 'as' => 'download']);
Route::get('/{logViewerFile}/raw', ['uses' => 'LogViewerController@raw', 'as' => 'raw']);
Route::get('/{logViewerFile?}', ['uses' => 'LogViewerController@index', 'as' => 'index']);
Route::delete('/{logViewerFile}', ['uses' => 'LogViewerController@destroy', 'as' => 'destroy']);
