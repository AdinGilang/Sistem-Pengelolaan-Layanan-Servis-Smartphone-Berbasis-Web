<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servis extends Model
{
    protected $table = 'servis';

    protected $fillable = [
        'kode_unik',
        'tanggal',
        'estimasi_selesai',
        'estimasi_hari',
        'estimasi_jam',
        'pelanggan',
        'alamat',
        'no_wa',
        'merk_hp',
        'tipe_hp',
        'kelengkapan',
        'teknisi',
        'pola_kunci',
        'kata_sandi',
        'kerusakan',
        'status',
        'biaya',
    ];

    protected $casts = [
        'kelengkapan'      => 'array',
        'tanggal'          => 'date',
        'estimasi_selesai' => 'date',
    ];

    protected $hidden = [
        'kata_sandi',
    ];
}