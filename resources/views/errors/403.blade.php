<x-layouts.public title="Akses Ditolak" :noindex="true">
    <section class="error-page">
        <p class="error-page__code">403</p>
        <h1 class="error-page__title">Akses Ditolak</h1>
        <p class="error-page__text">Akun Anda tidak memiliki izin untuk membuka halaman ini. Bila menurut Anda ini keliru, hubungi administrator sistem.</p>
        <a href="{{ route('home') }}" class="btn btn--solid btn--lg">Kembali ke Beranda</a>
    </section>
</x-layouts.public>
