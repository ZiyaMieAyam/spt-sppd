@php
    $tempatTtd = $tempatTtd ?? 'Paringin';
    $jenisKop = $penandatangan['kop'] ?? 'diskominfo';
    $pejabatSementara = Config::get('pejabat-sementara.' . str_replace('-', '_', $penandatangan['jabatan_key'] ?? ''), []);
@endphp

<div class="ttd-wrapper">

    <div class="ttd">

        <table class="ttd-table">

            <tr>

                <td class="ttd-label">
                    Ditetapkan di
                </td>

                <td class="ttd-titik">
                    :
                </td>

                <td>
                    {{ $tempatTtd }}
                </td>

            </tr>


            <tr>

                <td class="ttd-label">
                    Pada Tanggal
                </td>

                <td class="ttd-titik">
                    :
                </td>

                <td>
                    {{ $tanggalTtd?->translatedFormat('d F Y') }}
                </td>

            </tr>

        </table>


        <div class="kepala-dinas">

            {{ $penandatangan['jabatan'] }}

            <div class="ruang-tanda-tangan"></div>

            <div class="nama-kepala">
                @if(!empty($penandatangan['nama']))
                    {{ $penandatangan['nama'] }}
                @else
                    ( Nama Pejabat Belum Diatur )
                @endif
            </div>

            @if(!empty($penandatangan['nip']))

                <div class="nip">
                    NIP. {{ $penandatangan['nip'] }}
                </div>

            @endif

        </div>

    </div>

</div>
