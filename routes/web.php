<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/stores/{store:slug}', [StoreController::class, 'show'])->name('stores.show');


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');

Route::get('auth/google/callback', [LoginController::class, 'handleProviderCallback']);

require __DIR__ . '/auth.php';
