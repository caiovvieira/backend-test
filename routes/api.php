<?php

use App\Http\Controllers\Redirect;
use App\Http\Controllers\RedirectLog;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/redirects', [Redirect::class, 'index']);
Route::post('/redirects', [Redirect::class, 'store']);
Route::put('/redirects/{redirect}', [Redirect::class, 'update']);
Route::delete('/redirects/{redirect}', [Redirect::class, 'delete']);

Route::get('/redirects/{redirect}/logs', [RedirectLog::class, 'logs']);
Route::get('/redirects/{redirect}/stats', [RedirectLog::class, 'stats']);


