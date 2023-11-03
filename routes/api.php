<?php

use App\Http\Controllers\DataFormController;
use App\Http\Controllers\TableListController;
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

// Маршрут для сохранения данных из формы
Route::post('/applications', [DataFormController::class,'saveDataAndCreateGoogleSheet']);

// Маршрут для получения списка таблиц
Route::get('/tables', [TableListController::class,'getTableList']);
