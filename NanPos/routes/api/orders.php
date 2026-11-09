<?php

use App\Http\Controllers\Dashboard\OrdersController;
use App\Http\Middleware\NsRestrictAnyMiddleware;
use App\Http\Middleware\NsRestrictMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware( NsRestrictMiddleware::arguments( 'possoft.read.orders' ) )->group( function () {
    Route::get( 'orders/{id?}', [ OrdersController::class, 'getOrders' ] )->where( 'id', '[0-9]+' );
    Route::get( 'orders/payments', [ OrdersController::class, 'getSupportedPayments' ] );
    Route::get( 'orders/{id}/pos', [ OrdersController::class, 'getPosOrder' ] )->where( 'id', '[0-9]+' );
} );
$orderReadMiddleware = NsRestrictAnyMiddleware::class . ':possoft.read.orders,possoft.pos.read-all-orders,possoft.pos.read-own-orders';
Route::middleware( $orderReadMiddleware )->group( function (): void {
    Route::get( 'orders/{id}/products', [ OrdersController::class, 'getOrderProducts' ] )->where( 'id', '[0-9]+' );
    Route::get( 'orders/{order}/products/refunded', [ OrdersController::class, 'getOrderProductsRefunded' ] )->where( 'order', '[0-9]+' );
    Route::get( 'orders/{order}/refunds', [ OrdersController::class, 'getOrderRefunds' ] )->where( 'order', '[0-9]+' );
    Route::get( 'orders/{id}/payments', [ OrdersController::class, 'getOrderPayments' ] )->where( 'id', '[0-9]+' );
    Route::get( 'orders/{order}/instalments', [ OrdersController::class, 'getOrderInstalments' ] )->where( 'order', '[0-9]+' );
    Route::get( 'orders/{order}/print/{doc?}', [ OrdersController::class, 'printOrder' ] )->where( 'order', '[0-9]+' );
} );
Route::post( 'orders/{order}/instalments/{instalment}/pay', [ OrdersController::class, 'payInstalment' ] )->where( 'order', '[0-9]+' )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.orders-instalments' ) );
Route::post( 'orders/{order}/void', [ OrdersController::class, 'voidOrder' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.void.orders' ) );
Route::post( 'orders', [ OrdersController::class, 'create' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.create.orders' ) );
Route::post( 'orders/{id}/products', [ OrdersController::class, 'addProductToOrder' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.orders' ) );
Route::post( 'orders/{order}/processing', [ OrdersController::class, 'changeOrderProcessingStatus' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.orders' ) );
Route::post( 'orders/{order}/delivery', [ OrdersController::class, 'changeOrderDeliveryStatus' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.orders' ) );
Route::post( 'orders/{order}/payments', [ OrdersController::class, 'addPayment' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.make-payment.orders' ) );
Route::post( 'orders/{order}/refund', [ OrdersController::class, 'makeOrderRefund' ] )
    ->middleware( NsRestrictMiddleware::arguments( 'possoft.refund.orders' ) );
Route::post( 'orders/{order}/instalments', [ OrdersController::class, 'createInstalment' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.create.orders-instalments' ) );

Route::put( 'orders/{order}/instalments/{instalment}', [ OrdersController::class, 'updateInstalment' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.orders-instalments' ) );
Route::put( 'orders/{id}', [ OrdersController::class, 'updateOrder' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.orders' ) );

Route::delete( 'orders/{order}/instalments/{instalment}', [ OrdersController::class, 'deleteInstalment' ] )
    ->middleware( NsRestrictMiddleware::arguments( 'possoft.delete.orders-instalments' ) );
Route::delete( 'orders/{order}', [ OrdersController::class, 'deleteOrder' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.delete.orders' ) );
Route::delete( 'orders/{id}/products/{product_id}', [ OrdersController::class, 'deleteOrderProduct' ] )->where( 'id', '[0-9]+' )->middleware( NsRestrictMiddleware::arguments( 'possoft.update.orders' ) );
