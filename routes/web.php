<?php

use Illuminate\Support\Facades\Route;

// require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\ChunkReadFilter;
use App\Http\Controllers\ReadController;
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

// Route::post('/index', [ReadController::class, 'index']);
// Route::get('/index', [IndexController::class, 'index']);
// Route::post('index', 'App\Http\Controllers\ReadController@index');

Route::post('/read', [ReadController::class, 'index']);
