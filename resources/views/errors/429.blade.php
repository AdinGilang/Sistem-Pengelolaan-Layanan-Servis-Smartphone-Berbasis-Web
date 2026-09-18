<x-layouts.public title="Terlalu Banyak Permintaan" :noindex="true">
    <section class="error-page">
        <p class="error-page__code">429</p>
        <h1 class="error-page__title">Terlalu Banyak Permintaan</h1>
        <p class="error-page__text">Permintaan dari perangkat Anda terlalu sering dalam waktu singkat. Tunggu sebentar sebelum mencoba lagi.</p>
        <a href="{{ route('home') }}" class="btn btn--solid btn--lg">Kembali ke Beranda</a>
    </section>
</x-layouts.public>
