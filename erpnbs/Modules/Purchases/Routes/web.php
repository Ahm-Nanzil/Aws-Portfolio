<?php
use Modules\Purchases\Http\Controllers\VendorItemsController;
use Modules\Purchases\Http\Controllers\PurchaseRequestController;
use Modules\Purchases\Http\Controllers\QuotationsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('purchases')->group(function() {
    Route::get('/', 'PurchasesController@index');
    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
                'revalidate',
            ],
        ], function () {
            Route::resource('vendor_items', VendorItemsController::class);
            Route::resource('purchase_request', PurchaseRequestController::class);
            Route::resource('quotations', QuotationsController::class);

        }
    );
});
