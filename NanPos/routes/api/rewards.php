<?php

use App\Http\Controllers\Dashboard\RewardsSystemController;
use App\Http\Middleware\NsRestrictMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware( NsRestrictMiddleware::arguments( 'possoft.read.rewards' ) )->group( function () {
    Route::get( 'reward-system/{id}/rules', [ RewardsSystemController::class, 'getRules' ] );
    Route::get( 'reward-system/{id}/coupons', [ RewardsSystemController::class, 'getRegisterOrders' ] );
} );

Route::post( 'reward-system', [ RewardsSystemController::class, 'create' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.create.rewards' ) );
Route::delete( 'reward-system/{id}', [ RewardsSystemController::class, 'deleteRewardSystem' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.delete.rewards' ) );
Route::put( 'reward-system/{id}', [ RewardsSystemController::class, 'editRewardSystem' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.rewards' ) );
