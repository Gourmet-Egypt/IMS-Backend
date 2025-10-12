<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\GoodTypeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TemperatureRangeController;
use App\Http\Controllers\TransferRequestItemController;
use App\Http\Controllers\TransferRequestController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
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

        Route::post('/{transferRequest}/status', [TransferRequestController::class, 'changeStatus'])
            ->name('transfer-request.changeStatus');


        // TransferRequestsItems Routes
        Route::post('items', [TransferRequestItemController::class, 'store'])
            ->name('transfer-requests.items.store');

        Route::put('items/{transferRequest}', [TransferRequestItemController::class, 'update'])
            ->name('transfer-requests.items.update');

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

    });

});



// Admin Routes
Route::middleware(['auth:sanctum' , 'admin'])->group(function () {

    // Vehicle Routes
    Route::apiResource('vehicle-types', VehicleController::class);


    // GoodType Routes
    Route::apiResource('good-types', GoodTypeController::class);



    // Reason Routes
    Route::apiResource('reason', ReasonController::class);


    // Temperature Range Routes
    Route::apiResource('temperature-range', TemperatureRangeController::class);

});


Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

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








