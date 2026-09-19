@php
    $statusKelas = match ($servis?->status) {
        'Menunggu' => 'badge--menunggu',
        'Proses'   => 'badge--proses',
        'Selesai'  => 'badge--selesai',
        default    => 'badge--netral',
    };

    $langkah = match ($servis?->status) {
        'Proses'  => 2,
        'Selesai' => 3,
        default   => 1,
    };
@endphp

<x-layouts.public
    title="Cek Status Servis"
    description="Masukkan kode servis dari nota Anda untuk melihat progres perbaikan smartphone di Phone Repair: status pengerjaan, teknisi yang menangani, dan total biaya."
>
    {{--
        Halaman ini sebelumnya memuat kompiler Tailwind versi browser dari CDN.
        Ukurannya sekitar 300 KB JavaScript yang harus diunduh dan dijalankan
        lebih dulu, lalu menyusun CSS di sisi pengguna pada setiap kunjungan —
        padahal ini halaman publik yang paling sering dibuka pelanggan dari
        jaringan seluler. Sekarang seluruh gaya berasal dari berkas CSS yang
        sudah dikompilasi saat build, tanpa satu pun skrip pihak ketiga.

        Catatan ini sengaja ditulis sebagai komentar Blade, bukan komentar CSS,
        supaya tidak ikut terkirim ke browser.
    --}}
    @push('styles')
    <style>
        .lacak {
            max-width: 560px;
            margin: 0 auto;
            padding: 40px 20px 64px;
        }

        .lacak__intro {
            margin-bottom: 24px;
        }

        .lacak__title {
            font-family: var(--font-display);
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 8px;
            letter-spacing: -.01em;
        }

        .lacak__lead {
            color: var(--muted);
            font-size: 15px;
            margin: 0;
            line-height: 1.6;
        }

        .cari {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .cari__input {
            flex: 1 1 240px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: var(--r-sm);
            font-family: inherit;
            font-size: 15px;
            color: var(--text);
            background: var(--bg);
            transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
        }

        /* Hanya warna dan bayangan yang berubah; ukuran kolom dibiarkan
           tetap supaya tidak ada pergeseran tata letak saat difokuskan. */
        .cari__input:focus {
            border-color: var(--blue);
            background: var(--surface);
            box-shadow: 0 0 0 3px var(--blue-wash);
            outline: none;
        }

        .catatan {
            font-size: 12.5px;
            color: var(--muted);
            margin: 12px 0 0;
        }

        .pesan {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 16px 18px;
            border-radius: var(--r-md);
            margin-top: 16px;
        }

        .pesan--kosong {
            background: var(--red-wash);
            border: 1px solid rgba(185, 28, 28, .22);
        }

        .pesan__judul {
            font-size: 14px;
            font-weight: 700;
            color: var(--red);
            margin: 0 0 2px;
        }

        .pesan__teks {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
        }

        /* ── Kartu hasil ── */
        .hasil {
            margin-top: 20px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .hasil__head {
            background: var(--navy);
            color: #fff;
            padding: 18px 22px;
        }

        .hasil__kode {
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 700;
            letter-spacing: .02em;
            margin: 0;
        }

        .hasil__perangkat {
            font-size: 13px;
            color: rgba(255, 255, 255, .65);
            margin: 4px 0 0;
        }

        .hasil__status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 22px;
            border-bottom: 1px solid var(--line);
        }

        .hasil__status-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .rincian {
            padding: 18px 22px;
            display: grid;
            gap: 16px;
        }

        .rincian__label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin: 0 0 3px;
        }

        .rincian__nilai {
            font-size: 15px;
            font-weight: 600;
            color: var(--navy);
            margin: 0;
            overflow-wrap: anywhere;
        }

        .biaya {
            margin: 0 22px 18px;
            padding: 16px 18px;
            background: var(--bg);
            border-radius: var(--r-md);
        }

        .biaya__nilai {
            font-family: var(--font-display);
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin: 4px 0 0;
        }

        /* ── Garis waktu progres ── */
        .progres {
            padding: 0 22px 24px;
        }

        .progres__judul {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin: 0 0 14px;
        }

        .progres__item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .progres__rel {
            display: flex;
            flex-direction: column;
            align-items: center;
            align-self: stretch;
        }

        .progres__bulat {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            background: var(--line);
            color: var(--muted);
            flex-shrink: 0;
        }

        .progres__bulat svg { width: 12px; height: 12px; }

        .progres__item.is-done .progres__bulat { background: var(--green); color: #fff; }
        .progres__item.is-now  .progres__bulat { background: var(--blue);  color: #fff; }

        /* Denyut halus pada langkah yang sedang berjalan — menandai bahwa
           perbaikan masih bergerak, bukan berhenti. Hanya box-shadow pada
           lingkaran 24px, bukan elemen besar. */
        .progres__item.is-now .progres__bulat {
            animation: pulseRing 2.4s ease-in-out infinite;
        }

        .progres__garis {
            flex: 1;
            width: 2px;
            min-height: 22px;
            background: var(--line);
        }

        .progres__item.is-done .progres__garis {
            background: var(--green);
            /* Garis penghubung tahap yang sudah selesai tumbuh dari atas
               ke bawah memakai scaleY — bukan height, supaya tidak memicu
               perhitungan ulang tata letak tiap frame. */
            transform-origin: top;
            animation: growLine .45s cubic-bezier(.16, 1, .3, 1) both;
        }

        /* Tiap langkah muncul menyusul, bukan serentak. */
        .progres__item {
            animation: fadeUp .45s cubic-bezier(.16, 1, .3, 1) both;
        }

        .progres__item:nth-of-type(1) { animation-delay: .10s; }
        .progres__item:nth-of-type(2) { animation-delay: .19s; }
        .progres__item:nth-of-type(3) { animation-delay: .28s; }

        .progres__item:nth-of-type(1) .progres__garis { animation-delay: .30s; }
        .progres__item:nth-of-type(2) .progres__garis { animation-delay: .39s; }

        .progres__isi { padding-bottom: 16px; }

        .progres__nama {
            font-size: 14px;
            font-weight: 600;
            color: var(--muted);
            margin: 2px 0 2px;
        }

        .progres__item.is-done .progres__nama,
        .progres__item.is-now .progres__nama { color: var(--navy); }

        .progres__ket {
            font-size: 12.5px;
            color: var(--muted);
            margin: 0;
        }
    </style>
    @endpush

    <div class="lacak">
        <div class="lacak__intro anim-fade-up">
            <h1 class="lacak__title">Cek Status Servis</h1>
            <p class="lacak__lead">
                Masukkan kode servis yang tertera pada nota atau pesan WhatsApp dari staf kami
                untuk melihat progres perbaikan perangkat Anda.
            </p>
        </div>

        <div class="card anim-fade-up" style="animation-delay:.08s">
            <div class="card__body">
                <form method="GET" action="{{ route('servis.cek') }}" class="cari" data-loading>
                    <label class="sr-only" for="kode">Kode servis</label>
                    <input
                        id="kode"
                        class="cari__input"
                        type="text"
                        name="kode"
                        value="{{ request('kode') }}"
                        placeholder="SRV-{{ date('Y') }}-XXXXXXXX"
                        maxlength="40"
                        autocomplete="off"
                        required
                    >
                    <button type="submit" class="btn btn--solid">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                        Cek
                    </button>
                </form>

                @error('kode')
                    <p class="catatan" style="color:var(--red);">{{ $message }}</p>
                @enderror

                <p class="catatan">
                    Kode berformat SRV diikuti tahun dan delapan karakter unik.
                </p>
            </div>
        </div>

        @if ($dicari && ! $servis)
            <div class="pesan pesan--kosong anim-fade-up" role="status">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="var(--red)"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                     style="flex-shrink:0;margin-top:2px;">
                    <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>
                </svg>
                <div>
                    <p class="pesan__judul">Kode servis tidak ditemukan</p>
                    <p class="pesan__teks">
                        Periksa kembali penulisan kode Anda. Bila masih belum ketemu,
                        hubungi staf kami dengan membawa nota servis.
                    </p>
                </div>
            </div>
        @endif

        @if ($servis)
            <article class="hasil anim-fade-up">
                <header class="hasil__head">
                    <h2 class="hasil__kode">{{ $servis->kode_unik }}</h2>
                    <p class="hasil__perangkat">
                        {{ trim(($servis->merk_hp ?? '') . ' ' . ($servis->tipe_hp ?? '')) ?: 'Perangkat tidak dirinci' }}
                    </p>
                </header>

                <div class="hasil__status">
                    <span class="hasil__status-label">Status saat ini</span>
                    <span class="badge {{ $statusKelas }}">
                        {{-- Titik berdenyut menandai status yang masih berjalan.
                             Status "Selesai" tidak lagi berubah, jadi tidak
                             perlu terasa "hidup". --}}
                        @unless ($servis->status === 'Selesai')
                            <span class="pulse-dot" aria-hidden="true"
                                  style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                        @endunless
                        {{ $servis->status }}
                    </span>
                </div>

                <div class="rincian">
                    <div>
                        <p class="rincian__label">Nama pelanggan</p>
                        <p class="rincian__nilai">{{ $servis->pelanggan }}</p>
                    </div>

                    <div>
                        <p class="rincian__label">Keluhan kerusakan</p>
                        <p class="rincian__nilai">{{ $servis->kerusakan }}</p>
                    </div>

                    @if ($servis->teknisi)
                        <div>
                            <p class="rincian__label">Teknisi</p>
                            <p class="rincian__nilai">{{ $servis->teknisi }}</p>
                        </div>
                    @endif

                    @if ($servis->estimasi_selesai)
                        <div>
                            <p class="rincian__label">Perkiraan selesai</p>
                            <p class="rincian__nilai">{{ $servis->estimasi_selesai->translatedFormat('d F Y') }}</p>
                        </div>
                    @endif
                </div>

                @if ($servis->biaya)
                    <div class="biaya">
                        <p class="rincian__label">Total biaya</p>
                        <p class="biaya__nilai">Rp {{ number_format($servis->biaya, 0, ',', '.') }}</p>
                    </div>
                @endif

                <div class="progres">
                    <p class="progres__judul">Progres pengerjaan</p>

                    @php
                        $tahapan = [
                            1 => ['Perangkat diterima', 'Unit sudah dicatat dan diterima staf kami.'],
                            2 => ['Sedang diperbaiki', 'Teknisi sedang mengerjakan perbaikan.'],
                            3 => ['Siap diambil', 'Perbaikan selesai, perangkat menunggu diambil.'],
                        ];
                    @endphp

                    @foreach ($tahapan as $nomor => [$nama, $keterangan])
                        @php
                            $kelas = $nomor < $langkah ? 'is-done' : ($nomor === $langkah ? 'is-now' : '');
                        @endphp
                        <div class="progres__item {{ $kelas }}">
                            <div class="progres__rel">
                                <span class="progres__bulat">
                                    @if ($nomor < $langkah)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                             aria-hidden="true">
                                            <path d="m5 12 5 5L20 7"/>
                                        </svg>
                                    @else
                                        {{ $nomor }}
                                    @endif
                                </span>
                                @unless ($loop->last)
                                    <span class="progres__garis"></span>
                                @endunless
                            </div>
                            <div class="progres__isi">
                                <p class="progres__nama">{{ $nama }}</p>
                                <p class="progres__ket">{{ $keterangan }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        @endif
    </div>
</x-layouts.public>
