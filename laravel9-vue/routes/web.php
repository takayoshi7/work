<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';

//基本の書き方 laravel9版
use App\Http\Controllers\TestController;
    Route::controller(TestController::class)->group(function () {
    Route::get('/profile', 'profile');
    Route::post('/enameChange', 'enameChange');
    Route::post('/imgUp', 'imgUp')->middleware(['imgup']);
    Route::post('/imgDelete', 'imgDelete')->middleware(['imgdelete']);
    Route::post('/emailChange', 'emailChange');
    Route::post('/getAddress', 'getAddress');
    Route::post('/addressChange', 'addressChange');
    Route::post('/phoneChange', 'phoneChange');
    Route::get('/roleList', 'roleList');
    Route::get('/list1', function() { return view('list1');})->name('list1');
    Route::get('/list1Data', 'list1Data')->middleware(['authoritycheck']);
    Route::post('/insert1', 'insert1')->middleware(['authoritycheck']);
    Route::post('/edit1', 'edit1')->middleware(['authoritycheck']);
    Route::post('/delete1', 'delete1')->middleware(['authoritycheck']);
    Route::post('/empCsvIn', 'empCsvIn')->middleware(['csvup']);
    Route::post('/empCsvEx', 'empCsvEx')->middleware(['csvdown']);
    Route::get('/list2', function() { return view('list2');})->name('list2');
    Route::get('/list2Data', 'list2Data')->middleware(['authoritycheck']);
    Route::post('/insert2', 'insert2')->middleware(['authoritycheck']);
    Route::post('/edit2', 'edit2')->middleware(['authoritycheck']);
    Route::post('/delete2', 'delete2')->middleware(['authoritycheck']);
    Route::post('/deptCsvIn', 'deptCsvIn')->middleware(['csvup']);
    Route::post('/deptCsvEx', 'deptCsvEx')->middleware(['csvdown']);
    Route::post('/sortNumChange', 'sortNumChange');
    Route::get('/log', function() { return view('log');})->name('log');
    Route::get('/logData', 'logData');
    Route::get('/logList', 'logList');
    Route::post('/logCsvEx', 'logCsvEx')->middleware(['logcsvdown']);
    Route::get('/schedule', function() { return view('schedule');})->name('schedule');
    Route::get('/scheduler', 'scheduler')->middleware(['authoritycheck']);
    Route::get('/hourList', 'hourList');
    Route::get('/timeList', 'timeList');
    Route::get('/dayList', 'dayList');
    Route::post('/setting1', 'setting1')->middleware(['authoritycheck']);
    Route::post('/setting2', 'setting2')->middleware(['authoritycheck']);
    Route::post('/setting3', 'setting3')->middleware(['authoritycheck']);
});
