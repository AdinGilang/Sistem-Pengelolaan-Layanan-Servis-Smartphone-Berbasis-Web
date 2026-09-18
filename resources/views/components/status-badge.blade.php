@props(['status', 'dark' => false])

{{--
    Satu sumber kebenaran untuk warna status di seluruh aplikasi.

    Sebelum komponen ini ada, warna status "Proses" ditulis ulang secara
    manual di lima tempat berbeda dengan nilai yang saling tidak cocok:
    biru di Data Servis & Detail Servis, ungu di Dashboard, ungu dengan hex
    berbeda lagi di Laporan, dan biru lagi di Invoice. Admin yang berpindah
    antar halaman melihat warna status yang berubah-ubah untuk arti yang
    sama persis. Sekarang seluruh halaman memanggil komponen ini, jadi
    warnanya dijamin identik di mana pun ditampilkan.
--}}

@php
    // Varian "dark" dipakai di atas latar gelap (contoh: kop invoice dengan
    // gradasi navy). Rona warnanya tetap sama persis (amber/violet/hijau),
    // hanya kecerahannya disesuaikan supaya tetap kontras — bukan tebakan
    // filter CSS, tapi kelas warna terang yang memang dirancang untuk latar
    // gelap, dari rona yang sama.
    $akhiran = $dark ? '-dark' : '';

    $kelas = match ($status) {
        'Menunggu' => "badge--menunggu{$akhiran}",
        'Proses'   => "badge--proses{$akhiran}",
        'Selesai'  => "badge--selesai{$akhiran}",
        default    => "badge--netral{$akhiran}",
    };
@endphp

<span {{ $attributes->merge(['class' => "badge {$kelas}"]) }}>{{ $status }}</span>
