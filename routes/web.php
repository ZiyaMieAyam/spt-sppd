<?php

use App\Models\Spt;
use App\Models\Sppd;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
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

    Route::get('/', function () {
        return view('beranda');
    })->name('beranda');


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
        ->latest()
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
        ->latest()
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


    Route::get('/cek-data', function () {

        return view('cek-data', [
            'spts' => Spt::latest()->get(),

            'sppds' => Sppd::with([
                'spt',
                'pegawai',
            ])
            ->latest()
            ->get(),
        ]);

    })->name('cek-data');


    Route::get('/admin/spts/{spt}/pdf', [
        SptPdfController::class,
        'generate',
    ])->name('spts.pdf');

});