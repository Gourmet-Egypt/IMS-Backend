<?php

use App\Http\Controllers\App\ItemController;
use App\Http\Controllers\App\PurchaseOrderController;
use App\Http\Controllers\App\TransferRequestController;
use App\Http\Controllers\App\TransferRequestItemController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Dashboard\CashierController;
use App\Http\Controllers\Dashboard\GoodTypeController;
use App\Http\Controllers\Dashboard\ReasonController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\StoreController;
use App\Http\Controllers\Dashboard\TemperatureRangeController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\VehicleController;
use Illuminate\Support\Facades\Route;

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


Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('transfer-requests')->group(function () {

        // TransferRequests Routes
        Route::get('/', [TransferRequestController::class, 'index'])
            ->name('transfer-request.index');

        Route::post('/', [TransferRequestController::class, 'store'])
            ->name('transfer-request.store');

        Route::get('/{transferRequest}', [TransferRequestController::class, 'show'])
            ->name('transfer-request.show');

        Route::put('/{transferRequest}', [TransferRequestController::class, 'update'])
            ->name('transfer-request.update');

        Route::delete('/{transferRequest}', [TransferRequestController::class, 'destroy'])
            ->name('transfer-request.destroy');

        Route::post('/{transferRequest}/status', [TransferRequestController::class, 'createOrder'])
            ->name('transfer-request.createOrder');


        // TransferRequestsItems Routes
        Route::post('{transferRequest}/items', [TransferRequestItemController::class, 'storeOrUpdate']);

        Route::delete('items/{transferRequest}', [TransferRequestItemController::class, 'destroy'])
            ->name('transfer-requests.items.destroy');

    });

    // Items Routes
    Route::prefix('items')->group(function () {


        Route::get('{lookup}', [ItemController::class, 'show'])
            ->name('item.show');

    });

    // PurchaseOrders Routes
    Route::prefix('purchase-order')->group(function () {

        Route::get('', [PurchaseOrderController::class, 'index'])
            ->name('purchase-order.index');

        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
            ->name('purchase-order.show');

        Route::get('/all/offline', [PurchaseOrderController::class, 'offline'])
            ->name('purchase-order.offline');

        Route::post('/{purchaseOrder}/commit', [PurchaseOrderController::class, 'commitOrder'])
            ->name('purchase-orders.commit');

    });

    Route::prefix('purchase-order-entry')->group(function () {
        Route::get('/{purchaseOrderEntry}', [PurchaseOrderController::class, 'allInfos']);
        Route::put('/{purchaseOrderEntry}', [PurchaseOrderController::class, 'updateInfos']);
    });

});


// Admin Routes
Route::get('vehicle-types', [VehicleController::class, 'index']);
Route::get('good-types', [GoodTypeController::class, 'index']);
Route::get('reason', [ReasonController::class, 'index']);
Route::get('temperature-range', [TemperatureRangeController::class, 'index']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('vehicle-types', VehicleController::class)->except('index');
    Route::apiResource('good-types', GoodTypeController::class)->except('index');
    Route::apiResource('user', UserController::class);
    Route::apiResource('reason', ReasonController::class)->except('index')->except('index');
    Route::apiResource('temperature-range', TemperatureRangeController::class)->except('index');

});


Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::get('/items', [ItemController::class, 'index'])
    ->name('item.index');


Route::get('/cashiers', [CashierController::class, 'index'])
    ->middleware('guest')
    ->name('cashiers.index');

Route::get('/stores', [StoreController::class, 'index'])
    ->middleware('guest')
    ->name('store.index');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')->name('logout');;
});


Route::get('/test/{purchaseOrder}', [PurchaseOrderController::class, 'test']);

Route::prefix('reports')->group(function () {
    Route::get('/transfer_list/entry_details/{id}', [ReportController::class, 'entryDetails']);
    Route::get('/transfer_list', [ReportController::class, 'transferList']);
    Route::get('/transfer_status/{id}', [ReportController::class, 'transferStatus']);
    Route::get('/store', [ReportController::class, 'store']);
    Route::get('/all-stores', [ReportController::class, 'allStores']);

});

