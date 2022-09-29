<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelpersController as Helpers;
use App\Http\Controllers\Kraken;
use App\Http\Controllers\SyncController as Sync;
use App\Http\Controllers\Monitor;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LocatorController;

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
Route::post('/signin', [Kraken::class,'trySignin']);

Route::middleware('kraken')->group(function(){

    Route::prefix('kraken')->controller(Kraken::class)->group(function (){
        Route::post('setpass','setPassword');
    });

    Route::prefix('store/{sid}')
        ->where(['sid' => '[0-9]+'])
        ->middleware('usestore')
        ->group(function(){
            Route::get('/', [StoreController::class, 'index']);

            Route::prefix('warehouses')->controller(WarehouseController::class)->group(function(){
                Route::get('/', 'index');

                Route::prefix('/{wid}')
                    ->middleware('usewarehouse')
                    ->group(function(){
                        Route::get('/','open');
                        Route::get('structure','structure');
                        Route::post('structure','sectionate');
                        Route::get('products','products');
                        Route::get('resume','resume');

                        Route::prefix('section/{lid}')
                            ->controller(LocationController::class)
                            ->group(function(){
                                Route::get('/', 'open');
                                Route::get('structure', 'structure');
                                Route::post('structure','sectionate');
                                Route::get('resume','resume');
                        });
                });

                Route::post('/', 'create');
            });

            Route::prefix('/locator')
                // ->middleware('uselocator')
                ->controller(LocatorController::class)
                ->group(function(){
                    Route::get('location/{loc}', 'location');
                    Route::get('product/{code}', 'product');
                    Route::post('toggle', 'toggle');
            });
        });
});

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
