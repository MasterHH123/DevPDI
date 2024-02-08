<?php

use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PassRestoreController;

// Panel
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkShiftController;
use App\Http\Controllers\ProceedingController;
use App\Http\Controllers\ProceedingRecordController;
use App\Http\Controllers\ProceedingTemplateController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\AppLogController;

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
Route::get('/', function(){
    return redirect()->to('login');
});

// Auth
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('auth');
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Pass restore
Route::get('/forgot-pass', [PassRestoreController::class, 'forgotPass'])->name('forgot_restore');
Route::post('/restore-pass', [PassRestoreController::class, 'restorePass'])->name('restore_pass');

// Panel
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [PanelController::class, 'home'])->name('home');
    Route::get('/stats', [PanelController::class, 'stats']);

    Route::resource('/users', UserController::class);
    Route::patch('/users/{id}/restore', [UserController::class, 'restore']);

    Route::resource('/work-shifts', WorkShiftController::class);

    Route::resource('/citizens', CitizenController::class);
    Route::patch('/citizens/{id}/restore', [CitizenController::class, 'restore']);
    
    Route::resource('/proceedings', ProceedingController::class);
    Route::patch('/proceedings/{id}/restore', [ProceedingController::class, 'restore']);
    Route::patch('/proceedings/{id}/open', [ProceedingController::class, 'open']);
    Route::patch('/proceedings/{id}/close', [ProceedingController::class, 'close']);

    Route::resource('/proceeding-records', ProceedingRecordController::class);
    Route::patch('/proceeding-records/{id}/restore', [ProceedingRecordController::class, 'restore']);

    Route::resource('/proceeding-templates', ProceedingTemplateController::class);
    Route::patch('/proceeding-templates/{id}/restore', [ProceedingTemplateController::class, 'restore']);

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions_list');
    Route::get('/app-logs', [AppLogController::class, 'index']);
});
