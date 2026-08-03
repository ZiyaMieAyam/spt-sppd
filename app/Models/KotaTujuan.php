<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KotaTujuan extends Model
{
    protected $table = 'kota_tujuan';

    protected $fillable = [
        'nama',
    ];
}