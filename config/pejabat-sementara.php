<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Data Pejabat Sementara (untuk PDF)
    |--------------------------------------------------------------------------
    |
    | Data pejabat pemberi perintah & penanggung jawab teknis yang
    | belum tersedia secara dinamis di database. Data ini digunakan
    | sementara di PDF SPT/SPPD sampai ada source data resmi.
    |
    | SILAKAN GANTI data di bawah ini dengan data pejabat yang sebenarnya
    | begitu data resmi tersedia.
    |
    */

    'bupati' => [
        'nama'  => 'H. ABDUL HADI, S.Ag., M.I.Kom.',
        'nip'   => '197008141994031007',
        'jabatan' => 'BUPATI BALANGAN',
    ],

    'wakil_bupati' => [
        'nama'  => null,
        'nip'   => null,
        'jabatan' => 'WAKIL BUPATI BALANGAN',
    ],

    'sekda' => [
        'nama'  => null,
        'nip'   => null,
        'jabatan' => 'SEKRETARIS DAERAH KABUPATEN BALANGAN',
    ],

    'kepala_diskominfo' => [
        'nama'  => 'H. Syaifuddin Tailah, S.Pd, MM',
        'nip'   => '196704031994031015',
        'pangkat' => 'Pembina Utama Muda',
        'golongan' => 'IV/c',
        'jabatan' => 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN KABUPATEN BALANGAN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Penandatangan Tetap SPPD — Kepala Dinas
    |--------------------------------------------------------------------------
    |
    | SPPD selalu ditandatangani oleh Kepala Dinas (pemimpin dinas).
    | Tidak ada pemilihan penandatangan seperti SPT.
    |
    */

    'kepala_dinas' => [
        'nama'  => 'H. Syaifuddin Tailah, S.Pd, MM',
        'nip'   => '196704031994031015',
        'pangkat' => 'Pembina Utama Muda',
        'golongan' => 'IV/c',
        'jabatan' => 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN KABUPATEN BALANGAN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pejabat Pelaksana Teknis Kegiatan (halaman belakang SPPD)
    |--------------------------------------------------------------------------
    |
    | Pejabat ini menandatangani bagian belakang formulir SPPD
    | sebagai "Pejabat Pelaksana Teknis Kegiatan".
    | Ganti dengan data resmi pejabat yang berwenang.
    |
    */

    'pejabat_teknis' => [
        'nama'  => 'MAHPUDZ AMIN, SE',
        'nip'   => '198502142010011016',
        'pangkat' => 'Penata',
        'golongan' => 'III/c',
        'jabatan' => 'KEPALA SUB BAGIAN UMUM DAN KEPEGAWAIAN SELAKU PEJABAT PELAKSANA TEKNIS KEGIATAN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Umum Instansi
    |--------------------------------------------------------------------------
    */

    'instansi' => [
        'nama' => 'DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN',
        'singkatan' => 'DISKOMINFOSAN',
        'alamat' => 'Jalan Jenderal Ahmad Yani Km. 3,5 Telp/Fax. (0526) 2028434',
        'alamat_lengkap' => 'Jalan Jenderal Ahmad Yani Km. 3,5 Telp/Fax. (0526) 2028434, Kec. Paringin Selatan, Kabupaten Balangan',
        'website' => 'www.diskominfo.balangankab.go.id',
        'email' => 'diskominfo@balangankab.go.id',
    ],

];
