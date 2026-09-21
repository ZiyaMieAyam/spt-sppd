<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penandatangan extends Model
{
    protected $fillable = [
        'kunci',
        'jabatan',
        'nama',
        'nip',
        'pangkat',
        'golongan',
        'kop',
    ];
}
