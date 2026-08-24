@php($logoFallbackPath = public_path('images/logo-balangan.png'))

<div class="kop">

    @if(!empty($adaGambarKop))

        {{-- Kop gambar resmi (aset disediakan menyusul, path via config/penandatangan.php) --}}

        <img
            class="kop-gambar"
            src="{{ $pathKop }}"
            alt="Kop Surat"
        >

    @else

        {{-- Fallback sementara sampai aset kop resmi tersedia --}}

        <table class="kop-table">

            <tr>

                <td class="kop-logo">

                    @if(file_exists($logoFallbackPath))

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
                        Jalan Jenderal Ahmad Yani Km. 3,5 Telp/Fax. (0526) 2028434
                        Kec. Paringin Selatan<br>

                        Website : www.diskominfo.balangankab.go.id /
                        Email : diskominfo@balangankab.go.id
                    </div>

                </td>

            </tr>

        </table>

    @endif

</div>
