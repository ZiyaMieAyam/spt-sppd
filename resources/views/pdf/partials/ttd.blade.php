@php($tempatTtd = $tempatTtd ?? 'Paringin Selatan')

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
                {{ $penandatangan['nama'] ?: '( Nama Pejabat Belum Diatur )' }}
            </div>

            @if(!empty($penandatangan['nip']))

                <div class="nip">
                    NIP. {{ $penandatangan['nip'] }}
                </div>

            @endif

        </div>

    </div>

</div>
