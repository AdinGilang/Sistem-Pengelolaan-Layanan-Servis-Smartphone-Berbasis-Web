<?php

namespace App\Modules\Setting\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'footer_thanks'     => ['required', 'string', 'max:255'],
            'garansi_servis'    => ['required', 'string', 'max:255'],
            'batas_pengambilan' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Teks ini dicetak apa adanya di nota. Tag HTML dibuang di sini supaya
     * tidak ada markup yang ikut tersimpan ke database sejak awal.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(array_map(
            static fn (mixed $value): string => trim(strip_tags((string) $value)),
            $this->only(array_keys($this->rules())),
        ));
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'footer_thanks'     => 'ucapan terima kasih',
            'garansi_servis'    => 'keterangan garansi',
            'batas_pengambilan' => 'batas pengambilan',
        ];
    }
}
