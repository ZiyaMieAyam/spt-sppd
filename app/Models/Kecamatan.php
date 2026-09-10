<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = [
        'nama',
    ];

    public function spts()
    {
        return $this->hasMany(Spt::class);
    }

    public function desas()
    {
        return $this->hasMany(Desa::class);
    }
}