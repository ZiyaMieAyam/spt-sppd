@extends('layouts.app')

@section('title', ! empty($editMode) ? 'Edit SPT & SPPD - SiPerjadin' : 'Form SPT & SPPD - SiPerjadin')

@section('content')

    <div class="page-header">
        <h1>{{ ! empty($editMode) ? 'Edit SPT & SPPD' : 'Form SPT & SPPD' }}</h1>
        <p>Input data perjalanan dinas.</p>
    </div>

    @if (! empty($editMode) && isset($spt) && $spt)
        <div class="info-note">
            Nomor SPT tidak dapat diubah:
            <strong>{{ $spt->nomor_spt }}</strong>
        </div>
    @endif

    <div class="card">

        <form
            action="{{ ! empty($editMode)
                ? route('form.update', $sppd)
                : route('form.simpan') }}"
            method="POST"
        >

            @csrf

            @if (! empty($editMode))
                @method('PUT')
            @endif

            @php
                $jenisSelected = old('jenis_perjalanan', $spt->jenis_perjalanan ?? '');
                $selectedPegawai = old('pegawai_ids', $selectedPegawaiIds ?? []);
            @endphp

            <div class="form-grid">

                {{-- JENIS PERJALANAN --}}

                <div class="form-group">

                    <label for="jenis_perjalanan">
                        Jenis Perjalanan
                    </label>

                    <select
                        name="jenis_perjalanan"
                        id="jenis_perjalanan"
                        required
                    >

                        <option value="">
                            Pilih jenis perjalanan
                        </option>

                        <option
                            value="Dalam Daerah"
                            {{ $jenisSelected === 'Dalam Daerah' ? 'selected' : '' }}
                        >
                            Dalam Daerah
                        </option>

                        <option
                            value="Luar Daerah"
                            {{ $jenisSelected === 'Luar Daerah' ? 'selected' : '' }}
                        >
                            Luar Daerah
                        </option>

                    </select>

                    @error('jenis_perjalanan')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

                {{-- TANGGAL SPT --}}

                <div class="form-group">

                    <label for="tanggal_spt">
                        Tanggal SPT
                    </label>

                    <input
                        type="date"
                        name="tanggal_spt"
                        id="tanggal_spt"
                        value="{{ old(
                            'tanggal_spt',
                            isset($spt) ? $spt->tanggal_spt?->format('Y-m-d') : ''
                        ) }}"
                        required
                    >

                    @error('tanggal_spt')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

                {{-- TANGGAL BERANGKAT --}}

                <div class="form-group">

                    <label for="tanggal_berangkat">
                        Tanggal Berangkat
                    </label>

                    <input
                        type="date"
                        name="tanggal_berangkat"
                        id="tanggal_berangkat"
                        value="{{ old(
                            'tanggal_berangkat',
                            isset($spt) ? $spt->tanggal_berangkat?->format('Y-m-d') : ''
                        ) }}"
                        required
                    >

                    @error('tanggal_berangkat')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

                {{-- TANGGAL KEMBALI --}}

                <div class="form-group">

                    <label for="tanggal_kembali">
                        Tanggal Kembali
                    </label>

                    <input
                        type="date"
                        name="tanggal_kembali"
                        id="tanggal_kembali"
                        value="{{ old(
                            'tanggal_kembali',
                            isset($spt) ? $spt->tanggal_kembali?->format('Y-m-d') : ''
                        ) }}"
                        required
                    >

                    @error('tanggal_kembali')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

                {{-- INFO JADWAL PERJALANAN DINAS TERAKHIR (helper kecil di dekat field tanggal) --}}
                <div class="form-group full">
                    <div class="sppd-last-info">
                        @php
                            $lastJadwal = $lastPerjalananDinas ?? $lastSppd ?? null;
                        @endphp
                        @if (isset($lastJadwal) && $lastJadwal)
                            @php
                                $fmtBerangkat = \Carbon\Carbon::parse($lastJadwal->tanggal_berangkat)->locale('id')->translatedFormat('d F Y');
                                $fmtKembali = \Carbon\Carbon::parse($lastJadwal->tanggal_kembali)->locale('id')->translatedFormat('d F Y');
                            @endphp
                            <span>Jadwal perjalanan dinas terakhir: {{ $fmtBerangkat }} s/d {{ $fmtKembali }}</span>
                        @else
                            <span>Belum ada jadwal perjalanan dinas.</span>
                        @endif
                    </div>
                </div>

                {{-- PERIHAL --}}

                <div class="form-group full">

                    <label for="perihal">
                        Perihal
                    </label>

                    <textarea
                        name="perihal"
                        id="perihal"
                        rows="4"
                        placeholder="Masukkan perihal perjalanan dinas"
                        required
                    >{{ old('perihal', $spt->perihal ?? '') }}</textarea>

                    @error('perihal')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

                {{-- DASAR --}}

                <div class="form-group full">

                    <label for="dasar">
                        Dasar
                    </label>

                    <textarea
                        name="dasar"
                        id="dasar"
                        rows="4"
                        placeholder="Masukkan nomor, tanggal, dan perihal surat/telaahan yang menjadi dasar perjalanan dinas"
                    >{{ old('dasar', $spt->dasar ?? '') }}</textarea>

                    <small>
                        Rujukan Perbup/peraturan yang menjadi dasar perjalanan dinas.
                        Bisa multi-baris.
                    </small>

                    @error('dasar')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

                {{-- DALAM DAERAH : KECAMATAN + DESA --}}

                <div
                    id="dalam-daerah-fields"
                    class="conditional-fields full"
                >

                    <div class="form-grid">

                        <div class="form-group">

                            <label for="kecamatan_id">
                                Kecamatan
                            </label>

                            <select
                                name="kecamatan_id"
                                id="kecamatan_id"
                                class="searchable"
                            >

                                <option value="">
                                    Pilih kecamatan
                                </option>

                                @foreach ($kecamatans as $kecamatan)

                                    <option
                                        value="{{ $kecamatan->id }}"
                                        {{ old(
                                            'kecamatan_id',
                                            $spt->kecamatan_id ?? ''
                                        ) == $kecamatan->id
                                            ? 'selected'
                                            : '' }}
                                    >
                                        {{ $kecamatan->nama }}
                                    </option>

                                @endforeach

                            </select>

                            @error('kecamatan_id')
                                <span class="field-error">{{ $message }}</span>
                            @enderror

                        </div>

                        <div class="form-group">

                            <label for="desa">
                                Desa/Kelurahan
                            </label>

                            <select
                                name="desa"
                                id="desa"
                                class="searchable"
                            >
                                <option value="">
                                    Pilih desa/kelurahan
                                </option>
                            </select>

                            @error('desa')
                                <span class="field-error">{{ $message }}</span>
                            @enderror

                        </div>

                    </div>

                </div>

                {{-- LUAR DAERAH : KOTA TUJUAN --}}

                <div
                    id="luar-daerah-fields"
                    class="conditional-fields full"
                >

                    <div class="form-group">

                        <label for="kota_tujuan_id">
                            Kota Tujuan
                        </label>

                        <select
                            name="kota_tujuan_id"
                            id="kota_tujuan_id"
                            class="searchable"
                        >

                            <option value="">
                                Pilih kota tujuan
                            </option>

                            @foreach ($kotaTujuans as $kota)

                                <option
                                    value="{{ $kota->id }}"
                                    {{ old(
                                        'kota_tujuan_id',
                                        $spt->kota_tujuan_id ?? ''
                                    ) == $kota->id
                                        ? 'selected'
                                        : '' }}
                                >
                                    {{ $kota->nama }}
                                </option>

                            @endforeach

                        </select>

                        @error('kota_tujuan_id')
                            <span class="field-error">{{ $message }}</span>
                        @enderror

                    </div>

                </div>

                {{-- TEMPAT KEGIATAN (Dalam Daerah & Luar Daerah — field baru, satu kolom tempat_kegiatan) --}}

                <div
                    id="tempat-kegiatan-fields"
                    class="conditional-fields full"
                >

                    <div class="form-group">

                        <label for="tempat_kegiatan">
                            Tempat Kegiatan
                        </label>

                        <input
                            type="text"
                            name="tempat_kegiatan"
                            id="tempat_kegiatan"
                            value="{{ old('tempat_kegiatan', $spt->tempat_kegiatan ?? '') }}"
                            placeholder="Contoh: Hotel Aria Barito / Aula Kecamatan"
                        >

                        <small>
                            Nama tempat/lokasi kegiatan. Contoh untuk Dalam Daerah: Hotel Aria Barito. Untuk Luar Daerah: Hotel Bandung.
                        </small>

                        @error('tempat_kegiatan')
                            <span class="field-error">{{ $message }}</span>
                        @enderror

                    </div>

                </div>

                {{-- PEGAWAI YANG DITUGASKAN --}}

                <div class="form-group full">

                    <label for="pegawai_ids">
                        Pegawai Yang Ditugaskan
                    </label>

                    <select
                        name="pegawai_ids[]"
                        id="pegawai_ids"
                        multiple
                        required
                    >

                        @foreach ($pegawais as $pegawai)

                            <option
                                value="{{ $pegawai->id }}"
                                {{ in_array($pegawai->id, $selectedPegawai)
                                    ? 'selected'
                                    : '' }}
                            >
                                {{ $pegawai->nama }}
                            </option>

                        @endforeach

                    </select>

                    <small>
                        Ketik untuk mencari pegawai, lalu pilih satu atau beberapa pegawai.
                        Setiap pegawai terpilih akan mendapatkan SPPD masing-masing.
                    </small>

                    @error('pegawai_ids')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                </div>

            </div>

            @if ($errors->any())

                <div class="errors">

                    @foreach ($errors->all() as $error)

                        <div>{{ $error }}</div>

                    @endforeach

                </div>

            @endif

            <div class="form-actions">

                <a
                    href="{{ route('beranda') }}"
                    class="button secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="button"
                >
                    {{ ! empty($editMode) ? 'Update' : 'Simpan' }}
                </button>

            </div>

        </form>

    </div>

@endsection

@push('styles')
    <style>
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 24px;
        }

        .info-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
        }

        .form-group small {
            margin-top: 6px;
            color: #6b7280;
            font-size: 11px;
        }

        .field-error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 12px;
        }

        .sppd-last-info {
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 11.5px;
            color: #6b7280;
            line-height: 1.6;
            margin-top: -6px;
        }

        .sppd-last-info span {
            display: block;
        }

        .sppd-last-info span + span {
            margin-top: 2px;
        }

        .conditional-fields {
            display: none;
        }

        .form-actions {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .button {
            border: none;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 17px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .button.secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .button.secondary:hover {
            background: #e5e7eb;
        }

        .errors {
            margin-top: 20px;
            padding: 12px 15px;
            border-radius: 7px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 13px;
        }

        .errors div + div {
            margin-top: 4px;
        }

        /* ===== Tom Select: samakan dengan field lain ===== */

        .ts-wrapper {
            width: 100%;
        }

        .ts-wrapper .ts-control {
            border: 1px solid #d1d5db;
            border-radius: 7px;
            padding: 7px 13px;
            font-family: inherit;
            font-size: 13px;
            box-shadow: none;
            min-height: 42px;
        }

        .ts-wrapper.single .ts-control {
            padding: 11px 13px;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #2563eb;
            box-shadow: none;
        }

        .ts-wrapper .ts-control .item {
            background: #dbeafe;
            color: #1e40af;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            margin: 2px 4px 2px 0;
        }

        .ts-wrapper .ts-control .item .remove {
            color: #1e40af;
            margin-left: 6px;
            border-left: 1px solid #bfdbfe;
            padding-left: 6px;
        }

        .ts-wrapper .ts-control .item.active {
            background: #bfdbfe;
        }

        .ts-wrapper .ts-dropdown {
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            font-family: inherit;
            font-size: 13px;
            z-index: 50;
        }

        .ts-wrapper .ts-dropdown .ts-dropdown-content .option {
            padding: 8px 12px;
        }

        .ts-wrapper .ts-dropdown .ts-dropdown-content .option.active {
            background: #eff6ff;
            color: #1e40af;
        }

        .ts-wrapper .ts-dropdown .ts-dropdown-content .option.selected {
            background: #dbeafe;
            color: #1e40af;
        }

        .ts-wrapper .dropdown-input-wrap input {
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 13px;
        }

        @media (max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }
        }
    </style>
@endpush

@push('scripts')
    <link
        href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css"
        rel="stylesheet"
    >

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | PEGAWAI MULTI SELECT
            |--------------------------------------------------------------------------
            */

            new TomSelect('#pegawai_ids', {
                plugins: ['remove_button'],
                maxItems: null,
                create: false,
                closeAfterSelect: false,
                hideSelected: false,
                duplicates: false,
                placeholder: 'Cari dan pilih pegawai...',
                searchField: ['text'],
                sortField: {
                    field: 'text',
                    direction: 'asc'
                }
            });

            /*
            |--------------------------------------------------------------------------
            | DESA/KELURAHAN DEPENDENT ON KECAMATAN
            |--------------------------------------------------------------------------
            */

            const desaByKecamatan = @json(
                $kecamatans->mapWithKeys(function ($k) {
                    return [$k->id => $k->desas->pluck('nama')->sort()->values()->toArray()];
                })->toArray()
            );

            const desaSelected = @json(old('desa', $spt->desa ?? ''));

            const kecamatanEl = document.getElementById('kecamatan_id');
            const desaEl = document.getElementById('desa');

            const tomDesa = new TomSelect('#desa', {
                create: false,
                placeholder: 'Pilih desa/kelurahan',
                searchField: ['text'],
                maxOptions: 300,
            });

            function populateDesa(kecamatanId, keepSelected = false) {
                const list = desaByKecamatan[kecamatanId] || [];
                const currentValue = keepSelected ? tomDesa.getValue() : null;

                tomDesa.clear();
                tomDesa.clearOptions();

                tomDesa.addOption({value: '', text: 'Pilih desa/kelurahan'});

                list.forEach(function (nama) {
                    tomDesa.addOption({value: nama, text: nama});
                });

                tomDesa.refreshOptions(false);

                // Tentukan nilai yang harus dipilih
                let targetValue = '';
                if (keepSelected && currentValue && list.includes(currentValue)) {
                    targetValue = currentValue;
                } else if (!keepSelected && desaSelected && list.includes(desaSelected)) {
                    // Initial load: gunakan old/spt value jika cocok dengan kecamatan
                    targetValue = desaSelected;
                }

                if (targetValue) {
                    tomDesa.setValue(targetValue, true);
                } else {
                    tomDesa.setValue('', true);
                }
            }

            // TomSelect untuk Kecamatan - dengan callback ganti desa
            const tomKecamatan = new TomSelect('#kecamatan_id', {
                create: false,
                placeholder: 'Cari kecamatan...',
                searchField: ['text'],
                onChange: function (value) {
                    // Reset desa ketika kecamatan diganti, jangan pertahankan pilihan lama
                    populateDesa(value, false);
                }
            });

            // Init desa berdasarkan kecamatan terpilih saat load (edit / old input)
            const initialKecamatanId = kecamatanEl.value || tomKecamatan.getValue();
            if (initialKecamatanId) {
                populateDesa(initialKecamatanId, false);
                // Jika editing dan desaSelected tidak ada di list (kecamatan tidak cocok), tetap kosong
                // Jika desaSelected kosong (create), tetap kosong
            }

            /*
            |--------------------------------------------------------------------------
            | KOTA TUJUAN
            |--------------------------------------------------------------------------
            */

            new TomSelect('#kota_tujuan_id', {
                create: false,
                placeholder: 'Cari kota tujuan...',
                searchField: ['text']
            });

            /*
            |--------------------------------------------------------------------------
            | CONDITIONAL FIELDS (Dalam Daerah / Luar Daerah)
            |--------------------------------------------------------------------------
            */

            const jenis = document.getElementById('jenis_perjalanan');
            const dalamDaerah = document.getElementById('dalam-daerah-fields');
            const luarDaerah = document.getElementById('luar-daerah-fields');
            const tempatKegiatan = document.getElementById('tempat-kegiatan-fields');

            function updateTujuanFields() {
                if (jenis.value === 'Dalam Daerah') {
                    dalamDaerah.style.display = 'block';
                    luarDaerah.style.display = 'none';
                    tempatKegiatan.style.display = 'block';
                } else if (jenis.value === 'Luar Daerah') {
                    dalamDaerah.style.display = 'none';
                    luarDaerah.style.display = 'block';
                    tempatKegiatan.style.display = 'block';
                } else {
                    dalamDaerah.style.display = 'none';
                    luarDaerah.style.display = 'none';
                    tempatKegiatan.style.display = 'none';
                }
            }

            jenis.addEventListener('change', updateTujuanFields);

            updateTujuanFields();

        });
    </script>
@endpush