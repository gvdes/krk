<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelpersController as Helpers;
use App\Http\Controllers\SyncController as Sync;
use App\Http\Controllers\Monitor;

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

Route::get('/', fn() => response("==> Kraken its running <==",401));

Route::prefix('helpers')->controller(Helpers::class)->group(function(){
    Route::get('/',fn() => response("it Works!!",401) );
    Route::get('pinger', 'pinger');
});

Route::prefix('sync')->controller(Sync::class)->group(function(){
    Route::patch('products', 'products');
});



Route::prefix('monitor')->controller(Monitor::class)->group(function(){
    Route::get('/','index');
});
