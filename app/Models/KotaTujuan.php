<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KotaTujuan extends Model
{
    protected $table = 'kota_tujuans';

    protected $fillable = [
        'nama',
    ];

    public function spts()
    {
        return $this->hasMany(Spt::class);
    }
}
