<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PartOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        if (!auth()->user()->isCEO()) {
            return redirect()->route('reports.index');
        }
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('ceo')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::resource('reports', ReportController::class);
    Route::get('reports/{report}/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::middleware('approver')->group(function () {
        Route::post('/reports/{report}/approve', [ReportController::class, 'approve'])->name('reports.approve');
        Route::post('/reports/{report}/reject', [ReportController::class, 'reject'])->name('reports.reject');
    });

    Route::resource('partorders', PartOrderController::class);
    Route::middleware('approver')->group(function () {
        Route::post('/partorders/{partorder}/approve', [PartOrderController::class, 'approve'])->name('partorders.approve');
        Route::post('/partorders/{partorder}/reject', [PartOrderController::class, 'reject'])->name('partorders.reject');
    });

    Route::resource('workrequests', WorkRequestController::class);
    Route::middleware('approver')->group(function () {
        Route::post('/workrequests/{workrequest}/approve', [WorkRequestController::class, 'approve'])->name('workrequests.approve');
        Route::post('/workrequests/{workrequest}/reject', [WorkRequestController::class, 'reject'])->name('workrequests.reject');
    });
    Route::middleware('ceo')->group(function () {
        Route::post('/workrequests/{workrequest}/financial', [WorkRequestController::class, 'updateFinancial'])->name('workrequests.financial');
    });

    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::post('/', [CommentController::class, 'store'])->name('store');
        Route::get('/{type}/{id}', [CommentController::class, 'show'])->name('show');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });
});
