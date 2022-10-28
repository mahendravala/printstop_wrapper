<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClickpostController;

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

Route::get('/clickpost', [ClickpostController::class, 'index']);
Route::get('/call-the-command', [ClickpostController::class, 'call_the_command']);
Route::get('/clickpost/download-pdf', [ClickpostController::class, 'downloadPdf']);
Route::get('/clickpost/download-excel', [ClickpostController::class, 'downloadExcel']);
Route::post('/clickpost/post', [ClickpostController::class, 'post']);
Route::get(
    '/clickpost/recommendations', [ClickpostController::class, 'recommendations']);

Route::get(
    '/clickpost/delete', [ClickpostController::class, 'delete']);

#php artisan make:job RecAndManifest
