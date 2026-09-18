<x-layouts.public title="Terjadi Kesalahan" :noindex="true">
    <section class="error-page">
        <p class="error-page__code">500</p>
        <h1 class="error-page__title">Terjadi Kesalahan</h1>
        <p class="error-page__text">Ada gangguan pada sistem kami. Tim teknis sudah menerima catatannya. Silakan coba beberapa saat lagi.</p>
        <a href="{{ route('home') }}" class="btn btn--solid btn--lg">Kembali ke Beranda</a>
    </section>
</x-layouts.public>
