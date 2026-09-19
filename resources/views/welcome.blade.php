@php
    $faq = [
        [
            'tanya'  => 'Bagaimana cara melacak status servis saya?',
            'jawab'  => 'Buka halaman Cek Status Servis, lalu masukkan kode servis yang tertera pada nota atau pesan WhatsApp dari staf kami. Status terbaru langsung tampil tanpa perlu membuat akun.',
        ],
        [
            'tanya'  => 'Di mana saya menemukan kode servis?',
            'jawab'  => 'Kode servis dicetak pada nota penerimaan perangkat, berformat SRV diikuti tahun dan delapan karakter unik. Kode yang sama juga dikirim lewat WhatsApp saat perangkat Anda diterima.',
        ],
        [
            'tanya'  => 'Apakah data pribadi saya aman?',
            'jawab'  => 'Halaman pelacakan publik hanya menampilkan status perbaikan dan biaya. Alamat, nomor WhatsApp, pola kunci, dan PIN perangkat tidak pernah ditampilkan di sana. PIN disimpan sebagai hash satu arah dan pola kunci disimpan terenkripsi.',
        ],
        [
            'tanya'  => 'Berapa lama perbaikan biasanya selesai?',
            'jawab'  => 'Estimasi waktu diberikan saat perangkat diterima dan tercatat pada nota servis. Bila teknisi menemukan kerusakan lanjutan, kami menghubungi Anda lebih dulu sebelum melanjutkan pengerjaan.',
        ],
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'       => 'LocalBusiness',
                'name'        => config('seo.site_name'),
                'description' => config('seo.description'),
                'url'         => url('/'),
                'image'       => asset(config('seo.image')),
            ],
            [
                '@type'      => 'WebSite',
                'name'       => config('seo.site_name'),
                'url'        => url('/'),
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => route('servis.cek') . '?kode={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            [
                '@type'      => 'FAQPage',
                'mainEntity' => array_map(fn (array $item): array => [
                    '@type'          => 'Question',
                    'name'           => $item['tanya'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['jawab']],
                ], $faq),
            ],
        ],
    ];
@endphp

<x-layouts.public
    title="Lacak Servis Smartphone Anda"
    description="Phone Repair mencatat setiap perangkat yang masuk, memantau progres perbaikan, dan menerbitkan invoice digital. Lacak status servis Anda kapan saja cukup dengan kode servis."
    :schema="$schema"
>
    @push('styles')
    <style>
        /*
         * Halaman depan sengaja tidak lagi memakai gumpalan warna raksasa
         * ber-blur yang beranimasi tanpa henti. Tiga lingkaran 600px dengan
         * filter blur 80px memaksa GPU menggambar ulang seluruh layar di
         * setiap frame — di laptop kentang dan ponsel, kipas berputar dan
         * baterai terkuras hanya untuk latar belakang yang tidak dibaca
         * siapa pun. Kedalaman sekarang dibangun dari warna, garis, dan
         * bayangan tipis saja.
         */

        .hero {
            max-width: 1120px;
            margin: 0 auto;
            padding: 64px 24px 48px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
            gap: 56px;
            align-items: center;
        }

        .hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px;
            border-radius: 100px;
            background: var(--blue-wash);
            color: var(--blue-dark);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .02em;
            margin-bottom: 20px;
        }

        .hero__dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
        }

        .hero__title {
            font-family: var(--font-display);
            font-size: clamp(32px, 4.6vw, 52px);
            font-weight: 700;
            line-height: 1.12;
            letter-spacing: -.02em;
            margin: 0 0 18px;
            color: var(--navy);
        }

        .hero__title em {
            font-style: normal;
            color: var(--blue);
        }

        .hero__lead {
            font-size: 16px;
            line-height: 1.7;
            color: var(--muted);
            margin: 0 0 28px;
            max-width: 52ch;
        }

        .hero__cta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* ── Angka nyata dari basis data ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0;
            margin-top: 40px;
            border-top: 1px solid var(--line);
            padding-top: 24px;
        }

        .stats__item + .stats__item {
            border-left: 1px solid var(--line);
            padding-left: 20px;
        }

        .stats__value {
            font-family: var(--font-display);
            font-size: 30px;
            font-weight: 700;
            line-height: 1;
            color: var(--navy);
        }

        .stats__label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
            font-weight: 500;
        }

        /* ── Kartu pelacakan di sisi kanan ── */
        .tracker {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;

            /*
             * Dua animasi berurutan pada satu elemen: masuk sekali dari
             * kanan, lalu melayang halus tanpa henti. Ditulis di CSS (bukan
             * lewat kelas + animation-delay inline) karena nilai delay pada
             * shorthand berlaku per-animasi — menaruhnya di markup akan
             * membuat animasi kedua ikut memakai delay yang sama dan
             * bertabrakan dengan animasi masuk.
             *
             * Animasi melayang baru mulai setelah animasi masuk selesai,
             * dan karena keduanya menganimasikan transform, yang terakhir
             * di daftar inilah yang mengambil alih sesudahnya.
             */
            animation:
                floatInRight .6s cubic-bezier(.16, 1, .3, 1) .18s both,
                floatSoft 5.5s ease-in-out 1.6s infinite;
        }

        .tracker__head {
            background: var(--navy);
            color: #fff;
            padding: 18px 22px;
        }

        .tracker__title {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 600;
            margin: 0;
        }

        .tracker__sub {
            font-size: 12px;
            color: rgba(255, 255, 255, .62);
            margin: 4px 0 0;
        }

        .tracker__body {
            padding: 22px;
        }

        .tracker__field {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
        }

        .tracker__input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: var(--r-sm);
            font-family: inherit;
            font-size: 15px;
            color: var(--text);
            background: var(--bg);
            transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
        }

        /* Hanya warna dan bayangan yang berubah — ukuran maupun posisi
           input dibiarkan tetap, supaya tidak ada pergeseran tata letak
           saat kolom ini mendapat fokus. */
        .tracker__input:focus {
            border-color: var(--blue);
            background: var(--surface);
            box-shadow: 0 0 0 3px var(--blue-wash);
            outline: none;
        }

        .tracker__hint {
            font-size: 12px;
            color: var(--muted);
            margin: 10px 0 0;
        }

        .tracker__steps {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .step {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .step__num {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--blue-wash);
            color: var(--blue-dark);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .step__title {
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
            margin: 2px 0 2px;
        }

        .step__text {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
            line-height: 1.5;
        }

        /* ── Fitur ── */
        .section {
            max-width: 1120px;
            margin: 0 auto;
            padding: 56px 24px;
        }

        .section__title {
            font-family: var(--font-display);
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 8px;
            letter-spacing: -.01em;
        }

        .section__lead {
            color: var(--muted);
            font-size: 15px;
            margin: 0 0 32px;
            max-width: 60ch;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }

        .feature {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .feature:hover {
            border-color: rgba(59, 91, 219, .35);
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }

        /* Jeda bertahap murni CSS — kartu pertama muncul lebih dulu,
           lalu menyusul satu per satu saat digulir. */
        .features .reveal:nth-child(1) { transition-delay: 0ms; }
        .features .reveal:nth-child(2) { transition-delay: 70ms; }
        .features .reveal:nth-child(3) { transition-delay: 140ms; }
        .features .reveal:nth-child(4) { transition-delay: 210ms; }

        .feature__icon {
            width: 42px;
            height: 42px;
            border-radius: var(--r-md);
            background: var(--blue-wash);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            transition: transform .22s ease;
        }

        /* Ikon ikut membesar sangat tipis saat kartunya disorot — cukup
           untuk terasa hidup, tidak sampai menggeser teks di bawahnya
           karena transform tidak memengaruhi tata letak. */
        .feature:hover .feature__icon { transform: scale(1.04); }

        .feature__icon svg {
            width: 21px;
            height: 21px;
        }

        .feature__title {
            font-size: 15px;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 6px;
        }

        .feature__text {
            font-size: 13.5px;
            color: var(--muted);
            line-height: 1.6;
            margin: 0;
        }

        /* ── FAQ ── */
        .faq {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .faq__item {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            padding: 0;
        }

        .faq__item summary {
            cursor: pointer;
            padding: 16px 20px;
            font-size: 15px;
            font-weight: 600;
            color: var(--navy);
            list-style: none;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
        }

        .faq__item summary::-webkit-details-marker { display: none; }

        .faq__item summary::after {
            content: '';
            width: 8px;
            height: 8px;
            border-right: 2px solid var(--muted);
            border-bottom: 2px solid var(--muted);
            transform: rotate(45deg);
            flex-shrink: 0;
            transition: transform .18s ease;
        }

        .faq__item[open] summary::after {
            transform: rotate(-135deg);
        }

        .faq__answer {
            padding: 0 20px 18px;
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            color: var(--muted);
        }

        /*
         * Buka/tutup yang halus untuk <details>.
         *
         * Tingginya dianimasikan lewat grid-template-rows 0fr → 1fr, bukan
         * max-height dengan angka tebakan. Bedanya penting: jawaban FAQ
         * panjangnya berbeda-beda, dan max-height yang ditebak terlalu
         * besar membuat animasi terasa "menggantung" di akhir, sementara
         * yang terlalu kecil memotong teks. Pendekatan grid menyesuaikan
         * tinggi sesungguhnya berapa pun isinya.
         *
         * Tanpa JavaScript, <details> tetap berfungsi seperti biasa —
         * hanya terbuka seketika tanpa transisi.
         */
        .faq__panel {
            display: grid;
            grid-template-rows: 1fr;
            transition: grid-template-rows .26s cubic-bezier(.16, 1, .3, 1);
        }

        .faq__panel > * {
            overflow: hidden;
            min-height: 0;
            transition: opacity .2s ease;
        }

        .faq__item[data-menutup="true"] .faq__panel > * { opacity: 0; }

        .faq .reveal:nth-child(1) { transition-delay: 0ms; }
        .faq .reveal:nth-child(2) { transition-delay: 60ms; }
        .faq .reveal:nth-child(3) { transition-delay: 120ms; }
        .faq .reveal:nth-child(4) { transition-delay: 180ms; }
        .faq .reveal:nth-child(n+5) { transition-delay: 220ms; }

        @media (max-width: 900px) {
            .hero {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 40px 20px 32px;
            }

            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px 0;
            }

            .stats__item:nth-child(odd) {
                border-left: none;
                padding-left: 0;
            }

            .section { padding: 40px 20px; }
        }
    </style>
    @endpush

    {{-- ══════════════════ HERO ══════════════════ --}}
    <section class="hero">
        <div>
            <p class="hero__eyebrow anim-fade-up">
                <span class="hero__dot pulse-dot" aria-hidden="true"></span>
                Layanan servis smartphone
            </p>

            <h1 class="hero__title anim-fade-up" style="animation-delay:.08s">
                Lacak servis HP Anda,<br>
                <em>tanpa perlu bertanya-tanya</em>
            </h1>

            <p class="hero__lead anim-fade-up" style="animation-delay:.16s">
                Setiap perangkat yang masuk ke Phone Repair mendapat kode servis sendiri.
                Cukup masukkan kodenya untuk melihat progres perbaikan, teknisi yang menangani,
                dan biaya akhirnya — kapan saja, dari perangkat apa saja.
            </p>

            <div class="hero__cta anim-fade-up" style="animation-delay:.24s">
                <a href="{{ route('servis.cek') }}" class="btn btn--solid btn--lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    Cek Status Servis
                </a>

                {{-- Tombol masuk staf / Dashboard sengaja tidak ditampilkan di
                     sini, tanpa terkecuali untuk staf yang sedang login.
                     Halaman ini murni pelanggan; staf mendapat tautan
                     langsung ke /login secara terpisah. --}}
            </div>

            {{-- Angka di bawah ini dibaca langsung dari basis data, bukan angka
                 contoh yang ditulis di template seperti sebelumnya.

                 Atribut data-hitung menyimpan angka mentahnya untuk animasi
                 hitung-naik, sementara isi elemennya tetap berupa angka
                 final yang sudah diformat. Urutan ini disengaja: kalau
                 JavaScript gagal dimuat, yang terbaca pengunjung tetap
                 angka asli dari basis data, bukan nol. --}}
            <div class="stats anim-fade-up" style="animation-delay:.32s">
                <div class="stats__item">
                    <div class="stats__value" data-hitung="{{ $ringkasan['total'] }}">{{ number_format($ringkasan['total'], 0, ',', '.') }}</div>
                    <p class="stats__label">Total unit tercatat</p>
                </div>
                <div class="stats__item">
                    <div class="stats__value" data-hitung="{{ $ringkasan['menunggu'] }}">{{ number_format($ringkasan['menunggu'], 0, ',', '.') }}</div>
                    <p class="stats__label">Menunggu antrean</p>
                </div>
                <div class="stats__item">
                    <div class="stats__value" data-hitung="{{ $ringkasan['proses'] }}">{{ number_format($ringkasan['proses'], 0, ',', '.') }}</div>
                    <p class="stats__label">Sedang dikerjakan</p>
                </div>
                <div class="stats__item">
                    <div class="stats__value" data-hitung="{{ $ringkasan['selesai'] }}">{{ number_format($ringkasan['selesai'], 0, ',', '.') }}</div>
                    <p class="stats__label">Selesai diperbaiki</p>
                </div>
            </div>
        </div>

        {{-- Kartu ini bukan gambar hiasan: formulirnya benar-benar mengirim
             ke halaman pelacakan. --}}
        <div class="tracker">
            <div class="tracker__head">
                <h2 class="tracker__title">Lacak perbaikan Anda</h2>
                <p class="tracker__sub">Masukkan kode dari nota servis</p>
            </div>

            <div class="tracker__body">
                <form method="GET" action="{{ route('servis.cek') }}" data-loading>
                    <label class="tracker__field" for="kode-hero">Kode servis</label>
                    <input
                        id="kode-hero"
                        class="tracker__input"
                        type="text"
                        name="kode"
                        inputmode="text"
                        autocomplete="off"
                        maxlength="40"
                        placeholder="SRV-{{ date('Y') }}-XXXXXXXX"
                        required
                    >
                    <p class="tracker__hint">
                        Kode tercetak pada nota servis dan dikirim lewat WhatsApp saat perangkat diterima.
                    </p>
                    <button type="submit" class="btn btn--solid" style="width:100%;margin-top:14px;">
                        Lacak Sekarang
                    </button>
                </form>

                <div class="tracker__steps">
                    <div class="step">
                        <span class="step__num" aria-hidden="true">1</span>
                        <div>
                            <p class="step__title">Perangkat diterima</p>
                            <p class="step__text">Kelengkapan dicatat, Anda menerima nota dengan kode servis.</p>
                        </div>
                    </div>
                    <div class="step">
                        <span class="step__num" aria-hidden="true">2</span>
                        <div>
                            <p class="step__title">Dikerjakan teknisi</p>
                            <p class="step__text">Perubahan biaya di luar estimasi dikonfirmasi lebih dulu ke Anda.</p>
                        </div>
                    </div>
                    <div class="step">
                        <span class="step__num" aria-hidden="true">3</span>
                        <div>
                            <p class="step__title">Siap diambil</p>
                            <p class="step__text">Status berubah menjadi Selesai dan invoice diterbitkan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════ FITUR ══════════════════ --}}
    <section class="section" aria-labelledby="judul-fitur">
        <h2 class="section__title reveal" id="judul-fitur">Yang dikerjakan sistem ini</h2>
        <p class="section__lead">
            Seluruh proses servis yang dulu dicatat di buku tulis kini terekam rapi,
            dari perangkat masuk sampai invoice tercetak.
        </p>

        <div class="features">
            <article class="feature reveal">
                <div class="feature__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>
                <h3 class="feature__title">Pemantauan status</h3>
                <p class="feature__text">
                    Setiap unit punya status yang jelas: menunggu, sedang dikerjakan, atau selesai.
                    Pelanggan bisa memeriksanya sendiri tanpa menelepon.
                </p>
            </article>

            <article class="feature reveal">
                <div class="feature__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    </svg>
                </div>
                <h3 class="feature__title">Riwayat pelanggan</h3>
                <p class="feature__text">
                    Data pelanggan dan riwayat perbaikannya tersimpan terstruktur,
                    sehingga servis ulang tidak perlu mengulang pencatatan dari nol.
                </p>
            </article>

            <article class="feature reveal">
                <div class="feature__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/>
                    </svg>
                </div>
                <h3 class="feature__title">Invoice &amp; laporan</h3>
                <p class="feature__text">
                    Invoice dicetak langsung dalam format PDF siap kertas termal,
                    dan rekap bulanan dapat diunduh sebagai PDF maupun Excel.
                </p>
            </article>

            <article class="feature reveal">
                <div class="feature__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="feature__title">Akses berlapis</h3>
                <p class="feature__text">
                    Admin mengelola data servis, pemilik memantau laporan dan statistik.
                    Kredensial perangkat pelanggan disimpan terenkripsi.
                </p>
            </article>
        </div>
    </section>

    {{-- ══════════════════ FAQ ══════════════════ --}}
    <section class="section" aria-labelledby="judul-faq">
        <h2 class="section__title reveal" id="judul-faq">Pertanyaan yang sering diajukan</h2>
        <p class="section__lead">Hal-hal yang paling sering ditanyakan pelanggan sebelum menyervis perangkatnya.</p>

        <div class="faq">
            @foreach ($faq as $item)
                <details class="faq__item reveal" @if ($loop->first) open @endif>
                    <summary>{{ $item['tanya'] }}</summary>
                    {{-- Pembungkus ini yang tingginya dianimasikan; teks
                         jawabannya sendiri tidak berubah. --}}
                    <div class="faq__panel">
                        <div>
                            <p class="faq__answer">{{ $item['jawab'] }}</p>
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </section>
</x-layouts.public>
