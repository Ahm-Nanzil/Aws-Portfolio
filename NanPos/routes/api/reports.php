<?php

use App\Http\Controllers\Dashboard\ReportsController;
use App\Http\Middleware\NsRestrictMiddleware;
use Illuminate\Support\Facades\Route;

Route::post( 'reports/sale-report', [ ReportsController::class, 'getSaleReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.sales' ) );
Route::post( 'reports/sold-stock-report', [ ReportsController::class, 'getSoldStockReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.sales' ) );
Route::post( 'reports/profit-report', [ ReportsController::class, 'getProfit' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.sales' ) );
Route::post( 'reports/transactions', [ ReportsController::class, 'getAccountSummaryReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.transactions' ) );
Route::post( 'reports/annual-report', [ ReportsController::class, 'getAnnualReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.yearly' ) );
Route::post( 'reports/payment-types', [ ReportsController::class, 'getPaymentTypes' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.payment-types' ) );
Route::post( 'reports/products-report', [ ReportsController::class, 'getProductsReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.products-report' ) );
Route::post( 'reports/compute/{type}', [ ReportsController::class, 'computeReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.sales' ) );
Route::get( 'reports/cashier-report', [ ReportsController::class, 'getMyReport' ] );
Route::post( 'reports/low-stock', [ ReportsController::class, 'getLowStock' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.low-stock' ) );
Route::post( 'reports/stock-report', [ ReportsController::class, 'getStockReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.inventory' ) );
Route::post( 'reports/product-history-combined', [ ReportsController::class, 'getProductHistoryCombined' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.stock-history' ) );
Route::post( 'reports/customers-statement/{customer}', [ ReportsController::class, 'getCustomerStatement' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.customers-statement' ) );
Route::post( 'reports/compute-combined-report', [ ReportsController::class, 'computeCombinedReport' ] )->middleware( NsRestrictMiddleware::arguments( 'possoft.reports.sales' ) );
