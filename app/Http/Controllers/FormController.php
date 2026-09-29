<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\KotaTujuan;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use App\Services\ScheduleOverlapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    public function create()
    {
        $lastPerjalananDinas = ScheduleOverlapService::getLastPerjalananDinas();

        return view('form', [
            'pegawais' => Pegawai::orderBy('nama')->get(),
            'kecamatans' => Kecamatan::with('desas')->orderBy('nama')->get(),
            'kotaTujuans' => KotaTujuan::orderBy('nama')->get(),
            'selectedPegawaiIds' => [],
            'editMode' => false,
            'lastPerjalananDinas' => $lastPerjalananDinas,
            'lastSppd' => $lastPerjalananDinas, // alias kompatibilitas
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['pegawai_ids'] = array_values(array_unique(array_map('intval', $validated['pegawai_ids'])));

        DB::transaction(function () use ($validated) {
            $tanggalSpt = Carbon::parse($validated['tanggal_spt']);

            $spt = Spt::create([
                'jenis_perjalanan' => $validated['jenis_perjalanan'],
                'nomor_spt' => Spt::generateNomorSpt($tanggalSpt),
                'tanggal_spt' => $validated['tanggal_spt'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'perihal' => $validated['perihal'],
                'dasar' => $validated['dasar'] ?? null,
                'kecamatan_id' => $this->kecamatanId($validated),
                'desa' => $this->desa($validated),
                'kota_tujuan_id' => $this->kotaTujuanId($validated),
                'tempat_kegiatan' => $validated['tempat_kegiatan'] ?? null,
            ]);

            $urutan = Sppd::reserveNomorBlok(count($validated['pegawai_ids']), $tanggalSpt);

            foreach ($validated['pegawai_ids'] as $pegawaiId) {
                $pegawai = Pegawai::findOrFail($pegawaiId);

                Sppd::create([
                    'spt_id' => $spt->id,
                    'pegawai_id' => $pegawai->id,
                    'nomor_sppd' => Sppd::formatNomorSppd(
                        $pegawai->kode_sppd,
                        $urutan,
                        $tanggalSpt
                    ),
                    'tanggal_berangkat' => $validated['tanggal_berangkat'],
                    'tanggal_kembali' => $validated['tanggal_kembali'],
                ]);

                $urutan++;
            }
        });

        return redirect()
            ->route($this->routeTujuan($validated['jenis_perjalanan']))
            ->with('success', 'Data perjalanan dinas berhasil disimpan.');
    }

    public function edit(Sppd $sppd)
    {
        $spt = $sppd->spt;
        $excludeSptId = $spt?->id;
        $lastPerjalananDinas = ScheduleOverlapService::getLastPerjalananDinas($excludeSptId);

        return view('form', [
            'sppd' => $sppd,
            'spt' => $spt,
            'pegawais' => Pegawai::orderBy('nama')->get(),
            'kecamatans' => Kecamatan::with('desas')->orderBy('nama')->get(),
            'kotaTujuans' => KotaTujuan::orderBy('nama')->get(),
            'selectedPegawaiIds' => $spt
                ? array_values(array_unique($spt->sppds()->pluck('pegawai_id')->toArray()))
                : [],
            'editMode' => true,
            'lastPerjalananDinas' => $lastPerjalananDinas,
            'lastSppd' => $lastPerjalananDinas, // alias kompatibilitas
        ]);
    }

    public function update(Request $request, Sppd $sppd)
    {
        $validated = $this->validatedData($request);
        $validated['pegawai_ids'] = array_values(array_unique(array_map('intval', $validated['pegawai_ids'])));

        DB::transaction(function () use ($validated, $sppd) {
            $spt = $sppd->spt;

            if (! $spt) {
                throw ValidationException::withMessages([
                    'spt' => 'SPT terkait tidak ditemukan.',
                ]);
            }

            $spt->update([
                'jenis_perjalanan' => $validated['jenis_perjalanan'],
                'tanggal_spt' => $validated['tanggal_spt'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'perihal' => $validated['perihal'],
                'dasar' => $validated['dasar'] ?? null,
                'kecamatan_id' => $this->kecamatanId($validated),
                'desa' => $this->desa($validated),
                'kota_tujuan_id' => $this->kotaTujuanId($validated),
                'tempat_kegiatan' => $validated['tempat_kegiatan'] ?? null,
            ]);

            $pegawaiIds = $validated['pegawai_ids'];

            $existing = $spt->sppds()->get()->keyBy('pegawai_id');

            // Hapus SPPD yang pegawainya tidak lagi ditugaskan.
            $spt->sppds()->whereNotIn('pegawai_id', $pegawaiIds)->delete();

            // Pertahankan nomor_sppd pegawai yang tetap; hanya sinkronkan tanggal.
            foreach ($existing as $pegawaiId => $sppdRow) {
                if (in_array($pegawaiId, $pegawaiIds, true)) {
                    $sppdRow->update([
                        'tanggal_berangkat' => $validated['tanggal_berangkat'],
                        'tanggal_kembali' => $validated['tanggal_kembali'],
                    ]);
                }
            }

            // Buat SPPD hanya untuk pegawai baru agar nomor lama tidak berubah.
            $baruIds = array_values(array_diff($pegawaiIds, $existing->keys()->all()));

            if (! empty($baruIds)) {
                $tanggalSpt = Carbon::parse($validated['tanggal_spt']);
                $urutan = Sppd::reserveNomorBlok(count($baruIds), $tanggalSpt);

                foreach ($baruIds as $pegawaiId) {
                    $pegawai = Pegawai::findOrFail($pegawaiId);

                    Sppd::create([
                        'spt_id' => $spt->id,
                        'pegawai_id' => $pegawai->id,
                        'nomor_sppd' => Sppd::formatNomorSppd(
                            $pegawai->kode_sppd,
                            $urutan,
                            $tanggalSpt
                        ),
                        'tanggal_berangkat' => $validated['tanggal_berangkat'],
                        'tanggal_kembali' => $validated['tanggal_kembali'],
                    ]);

                    $urutan++;
                }
            }
        });

        return redirect()
            ->route($this->routeTujuan($validated['jenis_perjalanan']))
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Sppd $sppd)
    {
        DB::transaction(function () use ($sppd) {
            $spt = $sppd->spt;

            if ($spt) {
                $spt->sppds()->delete();
                $spt->delete();
            } else {
                $sppd->delete();
            }
        });

        return back()->with('success', 'Data berhasil dihapus.');
    }

    protected function validatedData(Request $request): array
    {
        $dalamDaerah = $request->input('jenis_perjalanan') === 'Dalam Daerah';
        $luarDaerah = $request->input('jenis_perjalanan') === 'Luar Daerah';

        return $request->validate([
            'jenis_perjalanan' => ['required', 'in:Dalam Daerah,Luar Daerah'],
            'tanggal_spt' => ['required', 'date'],
            'tanggal_berangkat' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_berangkat'],
            'perihal' => ['required', 'string'],
            'dasar' => ['nullable', 'string'],
            'pegawai_ids' => ['required', 'array', 'min:1'],
            'pegawai_ids.*' => ['integer', 'exists:pegawais,id', 'distinct'],
            'kecamatan_id' => [
                'nullable',
                'exists:kecamatans,id',
                Rule::requiredIf($dalamDaerah),
            ],
            'desa' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($dalamDaerah),
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    if ($request->input('jenis_perjalanan') === 'Dalam Daerah' && $value) {
                        $kecamatanId = $request->input('kecamatan_id');
                        if (! $kecamatanId) {
                            return;
                        }
                        $exists = Desa::where('nama', $value)
                            ->where('kecamatan_id', $kecamatanId)
                            ->exists();
                        if (! $exists) {
                            $fail('Desa/Kelurahan tidak valid untuk kecamatan terpilih.');
                        }
                    }
                },
            ],
            'kota_tujuan_id' => [
                'nullable',
                'exists:kota_tujuans,id',
                Rule::requiredIf($luarDaerah),
            ],
            'tempat_kegiatan' => ['nullable', 'string', 'max:255'],
        ], [
            'pegawai_ids.required' => 'Pilih minimal satu pegawai yang ditugaskan.',
            'pegawai_ids.min' => 'Pilih minimal satu pegawai yang ditugaskan.',
            'pegawai_ids.*.distinct' => 'Pegawai tidak boleh dipilih lebih dari satu kali.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih untuk perjalanan dalam daerah.',
            'desa.required' => 'Desa wajib diisi untuk perjalanan dalam daerah.',
            'kota_tujuan_id.required' => 'Kota tujuan wajib dipilih untuk perjalanan luar daerah.',
        ]);
    }

    protected function kecamatanId(array $validated): ?int
    {
        return $validated['jenis_perjalanan'] === 'Dalam Daerah'
            ? $validated['kecamatan_id']
            : null;
    }

    protected function desa(array $validated): ?string
    {
        return $validated['jenis_perjalanan'] === 'Dalam Daerah'
            ? $validated['desa']
            : null;
    }

    protected function kotaTujuanId(array $validated): ?int
    {
        return $validated['jenis_perjalanan'] === 'Luar Daerah'
            ? $validated['kota_tujuan_id']
            : null;
    }

    protected function routeTujuan(string $jenis): string
    {
        return $jenis === 'Dalam Daerah' ? 'dalam-daerah' : 'luar-daerah';
    }
}
