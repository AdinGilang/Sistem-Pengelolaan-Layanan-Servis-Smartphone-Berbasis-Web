<?php

namespace App\Modules\Servis\Requests;

use App\Modules\Servis\Models\Servis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Servis::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tanggal'          => ['required', 'date'],
            'estimasi_selesai' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'estimasi_hari'    => ['nullable', 'integer', 'min:0', 'max:365'],
            'estimasi_jam'     => ['nullable', 'integer', 'min:0', 'max:23'],
            'pelanggan'        => ['required', 'string', 'max:255'],
            'alamat'           => ['nullable', 'string', 'max:255'],
            // Hanya angka, spasi, tanda hubung, dan awalan + yang diterima.
            'no_wa'            => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s-]{6,19}$/'],
            'merk_hp'          => ['nullable', 'string', 'max:100'],
            'tipe_hp'          => ['nullable', 'string', 'max:100'],
            'kelengkapan'      => ['nullable', 'array', 'max:20'],
            'kelengkapan.*'    => ['string', 'max:50'],
            'teknisi'          => ['nullable', 'string', 'max:100'],
            // Pola kunci Android: urutan titik 1-9 dipisah tanda hubung.
            'pola_kunci'       => ['nullable', 'string', 'max:50', 'regex:/^[1-9](-[1-9]){0,8}$/'],
            'kata_sandi'       => ['nullable', 'digits_between:4,6'],
            'kerusakan'        => ['required', 'string', 'max:2000'],
            // Status dibatasi daftar resmi supaya data tidak bisa diisi
            // nilai sembarang yang merusak laporan dan statistik.
            'status'           => ['required', Rule::in(Servis::STATUSES)],
            'biaya'            => ['nullable', 'integer', 'min:0', 'max:999999999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'no_wa.regex'      => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890.',
            'pola_kunci.regex' => 'Pola kunci harus berupa urutan angka 1-9 dipisah tanda hubung. Contoh: 1-2-3-6-9.',
            'status.in'        => 'Status hanya boleh Menunggu, Proses, atau Selesai.',
        ];
    }

    /**
     * Rapikan input sebelum divalidasi: buang spasi berlebih dan
     * normalkan string kosong menjadi null.
     */
    protected function prepareForValidation(): void
    {
        $trimmed = [];

        foreach (['pelanggan', 'alamat', 'no_wa', 'merk_hp', 'tipe_hp', 'teknisi', 'pola_kunci', 'kerusakan'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $trimmed[$field] = $value === '' ? null : $value;
            }
        }

        if ($trimmed !== []) {
            $this->merge($trimmed);
        }
    }
}
