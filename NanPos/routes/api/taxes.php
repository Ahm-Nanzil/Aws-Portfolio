<?php

use App\Http\Controllers\Dashboard\TaxesController;
use App\Http\Middleware\NsRestrictMiddleware;
use Illuminate\Support\Facades\Route;

Route::post( 'taxes', [ TaxesController::class, 'post' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.create.taxes' ) );
Route::put( 'taxes/{id}', [ TaxesController::class, 'put' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.taxes' ) );
Route::delete( 'taxes/{id}', [ TaxesController::class, 'delete' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.delete.taxes' ) );

Route::middleware( NsRestrictMiddleware::arguments( 'possoft.read.taxes' ) )->group( function () {
    Route::get( 'taxes/{id?}', [ TaxesController::class, 'get' ] )->where( [ 'id' => '[0-9]+' ] );
    Route::get( 'taxes/groups/{id?}', [ TaxesController::class, 'getTaxGroup' ] );
} );
