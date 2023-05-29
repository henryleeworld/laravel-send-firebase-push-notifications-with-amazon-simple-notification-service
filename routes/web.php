<?php

use App\Http\Controllers\AmazonSimpleNotificationController;
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
Route::get('/', [AmazonSimpleNotificationController::class, 'index']);
Route::get('/send/sns/publish', [AmazonSimpleNotificationController::class, 'publish']);
Route::post('/device/token', [AmazonSimpleNotificationController::class, 'registerEndpoint']);
