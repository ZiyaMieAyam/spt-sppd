@extends('layouts.app')

@section('title', ! empty($editMode) ? 'Edit SPT & SPPD' : 'Form SPT & SPPD')

@section('content')

<div class="page-header">
    <h1>{{ ! empty($editMode) ? 'Edit SPT & SPPD' : 'Form SPT & SPPD' }}</h1>
    <p>Input data perjalanan dinas.</p>
</div>

@if(! empty($editMode) && isset($spt) && $spt)
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

        @if(! empty($editMode))
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

                            @foreach($kecamatans as $kecamatan)

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
                            Desa
                        </label>

                        <input
                            type="text"
                            name="desa"
                            id="desa"
                            value="{{ old('desa', $spt->desa ?? '') }}"
                            placeholder="Masukkan nama desa"
                        >

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

                        @foreach($kotaTujuans as $kota)

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

                    @foreach($pegawais as $pegawai)

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

        @if($errors->any())

            <div class="errors">

                @foreach($errors->all() as $error)

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
    | KECAMATAN
    |--------------------------------------------------------------------------
    */

    new TomSelect('#kecamatan_id', {
        create: false,
        placeholder: 'Cari kecamatan...',
        searchField: ['text']
    });

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

    function updateTujuanFields() {
        if (jenis.value === 'Dalam Daerah') {
            dalamDaerah.style.display = 'block';
            luarDaerah.style.display = 'none';
        } else if (jenis.value === 'Luar Daerah') {
            dalamDaerah.style.display = 'none';
            luarDaerah.style.display = 'block';
        } else {
            dalamDaerah.style.display = 'none';
            luarDaerah.style.display = 'none';
        }
    }

    jenis.addEventListener('change', updateTujuanFields);

    updateTujuanFields();

});

</script>

@endpush
