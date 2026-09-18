<x-layouts.public title="Halaman Tidak Ditemukan" :noindex="true">
    <section class="error-page">
        <p class="error-page__code">404</p>
        <h1 class="error-page__title">Halaman Tidak Ditemukan</h1>
        <p class="error-page__text">Alamat yang Anda tuju tidak tersedia. Mungkin tautannya sudah berubah atau halaman telah dipindahkan.</p>
        <a href="{{ route('home') }}" class="btn btn--solid btn--lg">Kembali ke Beranda</a>
    </section>
</x-layouts.public>
