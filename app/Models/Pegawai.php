<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = [
        'nip',
        'nama',
        'pangkat',
        'golongan',
        'jabatan',
        'kode_sppd',
        'unit_kerja',
        'status',
    ];

    public function sppds()
    {
        return $this->hasMany(Sppd::class);
    }

    public function spts()
    {
        return $this->belongsToMany(
            Spt::class,
            'sppds',
            'pegawai_id',
            'spt_id'
        );
    }
}
