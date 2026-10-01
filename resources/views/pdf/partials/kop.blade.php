@php
    $jenisKop = $penandatangan['kop'] ?? 'diskominfo';

    $garudaPath = public_path('images/garuda.png');
    $logoFallbackPath = public_path('images/logo-balangan.png');
@endphp

<div class="kop">

    {{-- Kop Bupati / Wakil Bupati --}}
    @if ($jenisKop === 'bupati')

        @if (!empty($adaGambarKop) && is_file($pathKop))
            <img
                class="kop-gambar"
                src="{{ $pathKop }}"
                alt="Kop Surat Bupati Balangan"
            >
        @else
            <div class="kop-bupati">
                @if (file_exists($garudaPath))
                    <img
                        class="kop-bupati-garuda"
                        src="{{ $garudaPath }}"
                        alt="Garuda Pancasila"
                    >
                @endif

                <div class="kop-bupati-teks">
                    BUPATI BALANGAN
                </div>
            </div>
        @endif

    {{-- Kop Sekretaris Daerah --}}
    @elseif ($jenisKop === 'sekda')

        @if (!empty($adaGambarKop) && is_file($pathKop))
            <img
                class="kop-gambar"
                src="{{ $pathKop }}"
                alt="Kop Surat Sekretariat Daerah"
            >
        @else
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if (file_exists($logoFallbackPath))
                            <img
                                src="{{ $logoFallbackPath }}"
                                alt="Logo Kabupaten Balangan"
                            >
                        @endif
                    </td>

                    <td class="kop-text">
                        <div class="kop-pemda">
                            PEMERINTAH KABUPATEN BALANGAN
                        </div>

                        <div class="kop-dinas">
                            SEKRETARIAT DAERAH
                        </div>

                        <div class="kop-alamat">
                            Jl. Jend. Ahmad Yani No. 01 Paringin.
                            Telp. (0526) 28060 Fax. (0526) 28408
                        </div>
                    </td>
                </tr>
            </table>
        @endif

    {{-- Kop Diskominfo --}}
    @else

        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if (file_exists($logoFallbackPath))
                        <img
                            src="{{ $logoFallbackPath }}"
                            alt="Logo Kabupaten Balangan"
                        >
                    @endif
                </td>

                <td class="kop-text">
                    <div class="kop-pemda">
                        PEMERINTAH KABUPATEN BALANGAN
                    </div>

                    <div class="kop-dinas">
                        DINAS KOMUNIKASI INFORMATIKA,<br>
                        STATISTIK DAN PERSANDIAN
                    </div>

                    <div class="kop-alamat">
                        Jalan Jenderal Ahmad Yani Km. 3,5
                        Telp/Fax. (0526) 2028434
                        Kec. Paringin Selatan<br>

                        Website : www.diskominfo.balangankab.go.id /
                        Email : diskominfo@balangankab.go.id
                    </div>
                </td>
            </tr>
        </table>

    @endif

</div>