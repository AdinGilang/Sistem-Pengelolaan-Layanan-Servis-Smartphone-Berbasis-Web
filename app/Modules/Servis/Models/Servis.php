<?php

namespace App\Modules\Servis\Models;

use App\Support\Casts\EncryptedOrPlain;
use Database\Factories\ServisFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Servis extends Model
{
    /** @use HasFactory<ServisFactory> */
    use HasFactory, SoftDeletes;

    /** Status yang diakui sistem. Dipakai validasi, filter, dan statistik. */
    public const STATUS_MENUNGGU = 'Menunggu';
    public const STATUS_PROSES   = 'Proses';
    public const STATUS_SELESAI  = 'Selesai';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_MENUNGGU,
        self::STATUS_PROSES,
        self::STATUS_SELESAI,
    ];

    protected $table = 'servis';

    /**
     * kode_unik sengaja tidak fillable: nilainya dibangkitkan server
     * lewat generateKodeUnik(), tidak boleh datang dari input pengguna.
     *
     * @var list<string>
     */
    protected $fillable = [
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

    /**
     * Kredensial perangkat milik pelanggan tidak pernah ikut serialisasi
     * (toArray/toJson), sehingga tidak bocor lewat response JSON mana pun.
     *
     * @var list<string>
     */
    protected $hidden = [
        'kata_sandi',
        'pola_kunci',
    ];

    protected function casts(): array
    {
        return [
            'kelengkapan'      => 'array',
            'tanggal'          => 'date',
            'estimasi_selesai' => 'date',
            'estimasi_hari'    => 'integer',
            'estimasi_jam'     => 'integer',
            'biaya'            => 'integer',
            // PIN disimpan sebagai hash bcrypt satu arah — hanya bisa diverifikasi.
            'kata_sandi'       => 'hashed',
            // Pola kunci harus bisa ditampilkan kembali ke teknisi, jadi
            // dienkripsi dua arah, bukan di-hash.
            'pola_kunci'       => EncryptedOrPlain::class,
        ];
    }

    /**
     * Kode servis acak yang dipakai pelanggan untuk melacak status.
     * 8 karakter Crockford-ish (tanpa huruf yang mudah tertukar) supaya
     * ruang tebakan cukup besar untuk endpoint publik /cek-servis.
     */
    public static function generateKodeUnik(): string
    {
        do {
            $kode = 'SRV-'.date('Y').'-'.Str::upper(Str::random(8));
        } while (static::withTrashed()->where('kode_unik', $kode)->exists());

        return $kode;
    }

    /** Filter pencarian nama pelanggan / kode servis. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        // Netralkan karakter wildcard LIKE agar input seperti "%" tidak
        // berubah menjadi pencarian "semua baris".
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);

        return $query->where(function (Builder $q) use ($escaped) {
            $q->where('pelanggan', 'like', "%{$escaped}%")
              ->orWhere('kode_unik', 'like', "%{$escaped}%");
        });
    }

    /** Filter status; nilai di luar daftar resmi diabaikan. */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return in_array($status, self::STATUSES, true)
            ? $query->where('status', $status)
            : $query;
    }

    /**
     * Model ini berada di dalam modul, bukan di App\Models, sehingga penebakan
     * nama factory bawaan Laravel tidak akan menemukannya. Ditunjuk manual.
     */
    protected static function newFactory(): Factory
    {
        return ServisFactory::new();
    }

    public function hasPin(): bool
    {
        return filled($this->getAttributes()['kata_sandi'] ?? null);
    }
}
