<?php

namespace App\Imports;

use App\Models\Pegawai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PegawaiImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    public int $importedRows = 0;

    /** @var array<int> */
    public array $skippedRows = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // Pengaman: NIP yang sudah ada tidak dibuat ulang,
            // sehingga data lama tidak berubah dan tidak error duplikat.
            if (Pegawai::where('nip', $row['nip'])->exists()) {
                $this->skippedRows[] = $index + 2;

                continue;
            }

            Pegawai::create([
                'nip' => $row['nip'],
                'nama' => $row['nama'],
                'pangkat' => $row['pangkat'] ?? null,
                'golongan' => $row['golongan'],
                'jabatan' => $row['jabatan'],
                'kode_sppd' => $row['kode_sppd'],
                'unit_kerja' => $row['unit_kerja'],
                'status' => $row['status'],
            ]);

            $this->importedRows++;
        }
    }

    /**
     * Normalisasi nilai sebelum divalidasi.
     */
    public function prepareForValidation(array $data, int $index): array
    {
        // Terima heading "Nama Pegawai" (menjadi nama_pegawai) sebagai alias "nama".
        if (! filled($data['nama'] ?? null) && filled($data['nama_pegawai'] ?? null)) {
            $data['nama'] = $data['nama_pegawai'];
        }

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // NIP yang terbaca Excel sebagai angka (mis. 12345.0) dijadikan string.
        $data['nip'] = $this->normalizeNumber($data['nip'] ?? null);

        // Kode SPPD yang terbaca sebagai angka (97.2) dinormalisasi ke format 097.2.
        if (isset($data['kode_sppd']) && preg_match('/^97\.[2-5]$/', (string) $data['kode_sppd'])) {
            $data['kode_sppd'] = '0'.$data['kode_sppd'];
        }

        // Toleransi variasi penulisan status.
        if (isset($data['status'])) {
            $data['status'] = match (strtolower(trim((string) $data['status']))) {
                'asn' => 'ASN',
                'pppk' => 'PPPK',
                'non asn', 'non-asn', 'nonasn' => 'Non ASN',
                default => $data['status'],
            };
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'max:255', 'unique:pegawais,nip'],
            'nama' => ['required', 'string', 'max:255'],
            'pangkat' => ['nullable', 'string', 'max:255'],
            'golongan' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'kode_sppd' => ['required', 'in:097.2,097.3,097.4,097.5'],
            'unit_kerja' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:ASN,PPPK,Non ASN'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'nip' => 'NIP',
            'nama' => 'Nama',
            'pangkat' => 'Pangkat',
            'golongan' => 'Golongan',
            'jabatan' => 'Jabatan',
            'kode_sppd' => 'Kode SPPD',
            'unit_kerja' => 'Unit Kerja',
            'status' => 'Status',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar di database.',
            'nama.required' => 'Nama wajib diisi.',
            'golongan.required' => 'Golongan wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'kode_sppd.required' => 'Kode SPPD wajib diisi.',
            'kode_sppd.in' => 'Kode SPPD harus salah satu dari 097.2, 097.3, 097.4, 097.5.',
            'unit_kerja.required' => 'Unit Kerja wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus salah satu dari ASN, PPPK, Non ASN.',
        ];
    }

    private function normalizeNumber(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_float($value)) {
            return number_format($value, 0, '', '');
        }

        return trim((string) $value);
    }
}
