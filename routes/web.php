<?php

use App\Http\Controllers\GoogleSheetsController;
use Illuminate\Support\Facades\Route;

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

Route::get('/google-sheets/auth', [GoogleSheetsController::class, 'auth'])->name('google-sheets.auth');
Route::get('/google-sheets/callback', [GoogleSheetsController::class, 'callback'])->name('google-sheets.callback');
Route::get('/google-sheets/data', [GoogleSheetsController::class, 'getData'])->name('google-sheets.data');
