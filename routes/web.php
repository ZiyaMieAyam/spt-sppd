<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\PerjalananDinasController;
use App\Http\Controllers\SppdPdfController;
use App\Http\Controllers\SptPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [
    AuthController::class,
    'showLogin',
])->name('login');

Route::post('/login', [
    AuthController::class,
    'login',
]);

Route::post('/logout', [
    AuthController::class,
    'logout',
])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/', [
        BerandaController::class,
        'index',
    ])->name('beranda');

    Route::get('/dalam-daerah', [
        PerjalananDinasController::class,
        'dalamDaerah',
    ])->name('dalam-daerah');

    Route::get('/luar-daerah', [
        PerjalananDinasController::class,
        'luarDaerah',
    ])->name('luar-daerah');

    // FORM CRUD

    Route::get('/form', [
        FormController::class,
        'create',
    ])->name('form');

    Route::post('/form', [
        FormController::class,
        'store',
    ])->name('form.simpan');

    Route::middleware('role:admin')->group(function () {

        Route::get('/form/{sppd}/edit', [
            FormController::class,
            'edit',
        ])->name('form.edit');

        Route::put('/form/{sppd}', [
            FormController::class,
            'update',
        ])->name('form.update');

        Route::delete('/form/spt/{spt}', [
            FormController::class,
            'destroySpt',
        ])->name('form.delete');

    });

    Route::get('/spts/{spt}/pdf/pilih', [
        SptPdfController::class,
        'pilih',
    ])->name('spts.pdf.pilih');

    Route::get('/spts/{spt}/pdf', [
        SptPdfController::class,
        'generate',
    ])->name('spts.pdf');

    Route::get('/sppds/{sppd}/pdf', [
        SppdPdfController::class,
        'show',
    ])->name('sppds.pdf');

});