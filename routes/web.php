<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\PartOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;
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
    Route::get('partorders/{partorder}/pdf', [PartOrderController::class, 'downloadPdf'])->name('partorders.pdf');
    Route::middleware('approver')->group(function () {
        Route::post('/partorders/{partorder}/approve', [PartOrderController::class, 'approve'])->name('partorders.approve');
        Route::post('/partorders/{partorder}/reject', [PartOrderController::class, 'reject'])->name('partorders.reject');
    });
    Route::middleware('checkrole:reception,ceo')->group(function () {
        Route::resource('workrequests', WorkRequestController::class);
        Route::get('workrequests/{workrequest}/pdf', [WorkRequestController::class, 'downloadPdf'])->name('workrequests.pdf');
        Route::middleware('approver')->group(function () {
            Route::post('/workrequests/{workrequest}/approve', [WorkRequestController::class, 'approve'])->name('workrequests.approve');
            Route::post('/workrequests/{workrequest}/reject', [WorkRequestController::class, 'reject'])->name('workrequests.reject');
        });
        Route::middleware('checkrole:ceo,reception')->group(function () {
            Route::post('/workrequests/{workrequest}/financial', [WorkRequestController::class, 'updateFinancial'])->name('workrequests.financial');
        });
    });
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::post('/', [CommentController::class, 'store'])->name('store');
        Route::get('/{type}/{id}', [CommentController::class, 'show'])->name('show');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });
    // Reports
    Route::prefix('reports/{report}/attachments')->name('reports.attachments.')->group(function () {
        Route::post('/',               [AttachmentController::class, 'storeForReport'])->name('store');
        Route::delete('/{attachment}', [AttachmentController::class, 'destroyForReport'])->name('destroy');
    });

    // Part Orders
    Route::prefix('part-orders/{partOrder}/attachments')->name('part-orders.attachments.')->group(function () {
        Route::post('/',               [AttachmentController::class, 'storeForPartOrder'])->name('store');
        Route::delete('/{attachment}', [AttachmentController::class, 'destroyForPartOrder'])->name('destroy');
    });

    // Work Requests
    Route::prefix('work-requests/{workRequest}/attachments')->name('work-requests.attachments.')->group(function () {
        Route::post('/',               [AttachmentController::class, 'storeForWorkRequest'])->name('store');
        Route::delete('/{attachment}', [AttachmentController::class, 'destroyForWorkRequest'])->name('destroy');
    });
    // routes/web.php
    Route::patch('workrequests/{workrequest}/stages/{stage}', [WorkRequestController::class, 'updateStage'])
        ->name('workrequests.stage.update');
    Route::post('workrequests/export-excel', [WorkRequestController::class, 'exportExcel'])
        ->name('workrequests.exportExcel');
    Route::post('partorders/export-excel', [PartOrderController::class, 'exportExcel'])
        ->name('partorders.exportExcel');

    Route::post('reports/export-excel', [ReportController::class, 'exportExcel'])
        ->name('reports.exportExcel');

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/',           [TaskController::class, 'index'])->name('index');
        Route::get('/create',     [TaskController::class, 'create'])->name('create');
        Route::post('/',          [TaskController::class, 'store'])->name('store');
        Route::get('/{task}',     [TaskController::class, 'show'])->name('show');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{task}',       [TaskController::class, 'destroy'])->name('destroy');
    });

    // پنل تعمیرکار
    Route::prefix('my-tasks')->name('my-tasks.')->group(function () {
        Route::get('/',           [TaskController::class, 'myTasks'])->name('index');
        Route::get('/{task}',     [TaskController::class, 'showMyTask'])->name('show');
    });

    Route::middleware(['auth', 'ceo'])->prefix('admin')->name('database.')->group(function () {
        Route::get('database/export', [DatabaseBackupController::class, 'export'])->name('export');
        Route::post('database/import', [DatabaseBackupController::class, 'import'])->name('import');
    });
});
