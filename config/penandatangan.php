<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Jenis Kop Surat
    |--------------------------------------------------------------------------
    |
    | Kunci jenis kop -> path gambar kop (relatif terhadap folder public/).
    | Ganti path di bawah ini begitu aset kop resmi tersedia.
    |
    | Belum tersedia:
    |   - public/images/kop-bupati.png  (kop Bupati Balangan)
    |   - public/images/kop-sekda.png   (kop Sekretariat Daerah Kab. Balangan)
    |
    | Selama file belum ada, PDF otomatis memakai kop teks sebagai fallback.
    |
    */

    'kop' => [
        'bupati' => [
            'gambar' => 'images/kop-bupati.png',
        ],

        'sekda' => [
            'gambar' => 'images/kop-sekda.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Daftar Penandatangan
    |--------------------------------------------------------------------------
    |
    | Kunci array dipakai sebagai nilai parameter `penandatangan` pada
    | request cetak PDF (whitelist server-side).
    |
    | Isi `nama` dan `nip` dengan data resmi pejabat yang berlaku.
    | Jangan mengarang nama pejabat: selama null, PDF menampilkan
    | placeholder yang jelas.
    |
    */

    'penandatangan' => [
        'bupati' => [
            'jabatan' => 'Bupati Balangan',
            'nama' => null,
            'nip' => null,
            'kop' => 'bupati',
        ],

        'wakil-bupati' => [
            'jabatan' => 'Wakil Bupati Balangan',
            'nama' => null,
            'nip' => null,
            'kop' => 'bupati',
        ],

        'sekda' => [
            'jabatan' => 'Sekretaris Daerah Kabupaten Balangan',
            'nama' => null,
            'nip' => null,
            'kop' => 'sekda',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Aturan Penandatangan Berdasarkan Jabatan Pegawai
    |--------------------------------------------------------------------------
    |
    | Validasi penandatangan SPT berdasarkan jabatan pegawai yang
    | ditugaskan (arahan Pak Chandra).
    |
    | null ATAU array kosong = semua penandatangan diperbolehkan
    | untuk sementara (aman secara default, mudah diperketat nanti).
    |
    | Contoh memperketat nanti (pola jabatan case-insensitive,
    | karakter * = wildcard):
    |
    | 'aturan_jabatan' => [
    |     [
    |         'cocok_jabatan' => ['Kepala Dinas'],
    |         'boleh'         => ['bupati', 'wakil-bupati'],
    |     ],
    |     [
    |         'cocok_jabatan' => ['Sekretaris*', 'Kepala Bidang'],
    |         'boleh'         => ['sekda'],
    |     ],
    | ],
    |
    | Semantik: setiap pegawai yang ditugaskan dicocokkan dengan aturan
    | pertama yang polanya cocok; pilihan penandatangan akhir adalah
    | irisan dari semua hasil pencocokan. Pegawai tanpa kecocokan tidak
    | membatasi pilihan.
    |
    */

    'aturan_jabatan' => null,

];
