<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SavingBoxController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('currencies', [CurrencyController::class, 'index'])->name('index');

Route::get('dashboard/crearCuenta', [AccountController::class, 'create'])->middleware('auth')->name('createAccount');
Route::post('dashboard/depositarPesos', [SavingBoxController::class, 'depositPesos'])->middleware('auth')->name('depositPesos');
Route::view('dashboard/crearCaja', 'createNewBox')->middleware('auth')->name('createSavingBox');
Route::post('dashboard/guardarNuevaCaja', [SavingBoxController::class, 'store'])->middleware('auth')->name('storeSavingBox');
Route::get('dashboard/ultimasTasasCambio', [CurrencyController::class, 'LatestRates'])->middleware('auth')->name('latestRates');
Route::get('dashboard/comprarMoneda', [SavingBoxController::class, 'showInfo'])->middleware('auth')->name('showBuyForm');
Route::get('dashboard/formularioCompra', [SavingBoxController::class, 'showInfo'])->middleware('auth')->name('showBuyForm');
Route::post('dashboard/guardarCompraMoneda', [SavingBoxController::class, 'buyCurrency'])->middleware('auth')->name('buyCurrency');
Route::get('dashboard/formularioVenta', [SavingBoxController::class, 'showInfo'])->middleware('auth')->name('showSellForm');
Route::post('dashboard/guardarVentaMoneda', [SavingBoxController::class, 'sellCurrency'])->middleware('auth')->name('sellCurrency');
