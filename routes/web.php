<?php

use App\Http\Controllers\Appcontroller;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [Appcontroller::class, 'index'])->name('home');

Route::match(['get', 'post'], '/process', [AppController::class, 'process'])->name('process');
