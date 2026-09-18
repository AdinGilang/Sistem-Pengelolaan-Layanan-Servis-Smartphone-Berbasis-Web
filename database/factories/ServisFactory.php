<?php

namespace Database\Factories;

use App\Modules\Servis\Models\Servis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Servis>
 */
class ServisFactory extends Factory
{
    protected $model = Servis::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(Servis::STATUSES);

        return [
            'kode_unik'   => Servis::generateKodeUnik(),
            'tanggal'     => now()->toDateString(),
            'pelanggan'   => fake()->name(),
            'alamat'      => fake()->streetAddress(),
            'no_wa'       => '08' . fake()->numerify('##########'),
            'merk_hp'     => fake()->randomElement(['Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme']),
            'tipe_hp'     => fake()->bothify('?#? ##'),
            'kelengkapan' => fake()->randomElements(['Baterai', 'Charger', 'SIM Card', 'Memori'], 2),
            'teknisi'     => fake()->firstName(),
            'kerusakan'   => fake()->randomElement(['LCD pecah', 'Baterai drop', 'Tidak bisa menyala', 'Konektor rusak']),
            'status'      => $status,
            'biaya'       => $status === Servis::STATUS_SELESAI ? fake()->numberBetween(50_000, 2_000_000) : null,
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn (): array => ['status' => $status]);
    }

    public function selesai(): static
    {
        return $this->state(fn (): array => [
            'status' => Servis::STATUS_SELESAI,
            'biaya'  => fake()->numberBetween(50_000, 2_000_000),
        ]);
    }

    /** Unit yang membawa kredensial perangkat. */
    public function denganPin(string $pin = '1234'): static
    {
        return $this->state(fn (): array => [
            'kata_sandi' => $pin,
            'pola_kunci' => '1-2-3-6-9',
        ]);
    }
}
