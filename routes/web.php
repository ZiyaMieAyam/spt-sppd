<?php

use App\Models\Sppd;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SptPdfController;
use App\Http\Controllers\SppdPdfController;
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


    Route::get('/dalam-daerah', function () {

        $data = Sppd::with([
            'spt.kecamatan',
            'pegawai',
        ])
        ->whereHas('spt', function ($query) {
            $query->where(
                'jenis_perjalanan',
                'Dalam Daerah'
            );
        })
        ->orderBy('spt_id', 'asc')
        ->orderBy('id', 'asc')
        ->get();

        return view('dalam-daerah', [
            'data' => $data,
        ]);

    })->name('dalam-daerah');


    Route::get('/luar-daerah', function () {

        $data = Sppd::with([
            'spt.kotaTujuan',
            'pegawai',
        ])
        ->whereHas('spt', function ($query) {
            $query->where(
                'jenis_perjalanan',
                'Luar Daerah'
            );
        })
        ->orderBy('spt_id', 'asc')
        ->orderBy('id', 'asc')
        ->get();

        return view('luar-daerah', [
            'data' => $data,
        ]);

    })->name('luar-daerah');


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


        Route::delete('/form/{sppd}', [
            FormController::class,
            'destroy',
        ])->name('form.delete');

    });

    Route::get('/admin/spts/{spt}/pdf/pilih', [
        SptPdfController::class,
        'pilih',
    ])->name('spts.pdf.pilih');


    Route::get('/admin/spts/{spt}/pdf', [
        SptPdfController::class,
        'generate',
    ])->name('spts.pdf');


    Route::get('/sppds/{sppd}/pdf', [
        SppdPdfController::class,
        'show',
    ])->name('sppds.pdf');

});