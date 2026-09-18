<x-app-layout>
    <x-slot name="header">Data Servis</x-slot>

    @push('styles')
    <style>
        /* Kolom Aksi — tombol ikon berwarna menggantikan tautan teks
           ("Detail Invoice Edit Hapus") yang dulu ditumpuk rapat tanpa
           jarak jelas, sulit dipindai sekilas di baris tabel yang padat. */
        .aksi-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .aksi-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: background-color .15s ease, transform .15s ease;
        }

        .aksi-btn svg { width: 15px; height: 15px; }
        .aksi-btn:hover { transform: translateY(-1px); }

        .aksi-btn--blue   { background: rgba(59, 91, 219, .1);  color: #3b5bdb; }
        .aksi-btn--blue:hover   { background: rgba(59, 91, 219, .18); }
        .aksi-btn--purple { background: rgba(147, 51, 234, .1); color: #9333ea; }
        .aksi-btn--purple:hover { background: rgba(147, 51, 234, .18); }
        .aksi-btn--amber  { background: rgba(180, 83, 9, .1);   color: #b45309; }
        .aksi-btn--amber:hover  { background: rgba(180, 83, 9, .18); }
        .aksi-btn--red    { background: rgba(185, 28, 28, .1);  color: #b91c1c; }
        .aksi-btn--red:hover    { background: rgba(185, 28, 28, .18); }
    </style>
    @endpush

    {{-- Tombol aksi ditentukan ServisPolicy, bukan lagi perbandingan string
         peran di dalam template. Sumber aturannya jadi satu dengan yang
         dipakai controller, sehingga tampilan dan izin sesungguhnya tidak
         bisa berbeda. --}}
    <div class="bg-white shadow-xl rounded-2xl p-6">

        <!-- Top Section -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">

            @can('create', \App\Modules\Servis\Models\Servis::class)
                <a href="{{ route('servis.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 transition text-white px-5 py-2 rounded-xl shadow-md inline-flex items-center gap-2 w-fit">
                    Tambah Servis
                </a>
            @else
                <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:rgba(245,159,0,0.1);border-radius:10px;font-size:13px;color:#d08700;font-weight:500;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Mode pemantauan: data hanya dapat dilihat
                </div>
            @endcan

        </div>

        {{-- Pencarian & filter.

             Sebelumnya kotak pencarian berdiri sendiri di luar <form> apa
             pun, murni mengandalkan JavaScript fetch(). Kalau skrip gagal
             dimuat, menekan Enter di kotak itu tidak melakukan apa-apa —
             tidak ada jalan mundur. Sekarang kotak pencarian ada DI DALAM
             form yang sama dengan filter status, jadi menekan Enter tetap
             mengirim pencarian sungguhan lewat GET biasa walau tanpa
             JavaScript sama sekali. Saat JavaScript aktif, skrip di
             partials.live-search cukup mencegat submit itu dan
             menggantinya dengan fetch() tanpa memuat ulang halaman. --}}
        <div class="mb-6">
            <form id="filter-form" method="GET" action="{{ route('servis.index') }}"
                  class="flex flex-col md:flex-row md:items-center flex-wrap gap-3">

                <div class="relative">
                    <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-400 focus-within:border-blue-400 transition bg-white">
                        <div class="pl-3 text-gray-400">
                            <svg id="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            {{-- Loading spinner (hidden by default) --}}
                            <svg id="search-spinner" class="hidden animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="#d1d5db" stroke-width="3"/>
                                <path d="M12 2a10 10 0 0 1 10 10" stroke="#3b82f6" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <label class="sr-only" for="live-search">Cari data servis</label>
                        <input
                            id="live-search"
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="Cari pelanggan, kode, merk HP..."
                            class="px-3 py-2 w-72 outline-none bg-transparent text-sm"
                            autocomplete="off"
                        >
                        <button id="clear-search"
                                class="{{ request('search') ? '' : 'hidden' }} pr-3 text-gray-400 hover:text-gray-600"
                                type="button"
                                aria-label="Bersihkan pencarian">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Hint text. aria-live memastikan pengguna pembaca layar
                         juga diberi tahu saat status pencarian berubah, bukan
                         hanya pengguna yang bisa melihat teksnya. --}}
                    <div id="search-hint" class="absolute right-0 mt-1 text-xs text-gray-400 hidden" aria-live="polite">
                        Mengetik...
                    </div>
                </div>

                <label class="sr-only" for="filter-status">Saring berdasarkan status</label>
                {{-- Pilihan status dibaca dari konstanta model, jadi menambah
                     status baru cukup di satu tempat. --}}
                <select name="status" id="filter-status" class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                <a href="{{ route('servis.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Reset</a>

                {{-- Info hasil. Diberi id supaya live-search bisa memperbarui
                     teks ini juga — sebelumnya info ini hanya dirender sekali
                     saat halaman dimuat penuh, lalu tidak pernah berubah lagi
                     ketika admin mengetik pencarian baru lewat AJAX, sehingga
                     angka dan kata kunci yang ditampilkan menjadi basi. --}}
                <span id="hasil-info" class="text-xs text-gray-500 italic">
                    @if(request('search'))
                        Hasil pencarian: <strong>"{{ request('search') }}"</strong>
                        — {{ $servis->total() }} data ditemukan
                    @endif
                </span>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto" id="table-wrapper">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Kode Servis</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Pelanggan</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">No. WA</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Merk / Tipe</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Teknisi</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="table-body">
                    @forelse($servis as $s)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Tanggal --}}
                            <td class="px-4 py-4 text-gray-500 whitespace-nowrap text-xs">
                                {{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '—' }}
                            </td>

                            {{-- Kode Servis --}}
                            <td class="px-4 py-4 font-semibold text-gray-700 whitespace-nowrap">
                                {{ $s->kode_unik }}
                            </td>

                            {{-- Pelanggan + Alamat --}}
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-800">{{ $s->pelanggan }}</div>
                                @if($s->alamat)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $s->alamat }}</div>
                                @endif
                            </td>

                            {{-- No WA --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($s->no_wa)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $s->no_wa)) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 text-green-600 hover:text-green-700 hover:underline font-medium text-xs">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        {{ $s->no_wa }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Merk / Tipe --}}
                            <td class="px-4 py-4">
                                @if($s->merk_hp || $s->tipe_hp)
                                    <div class="font-medium text-gray-700">{{ $s->merk_hp ?? '—' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $s->tipe_hp ?? '' }}</div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Teknisi --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($s->teknisi)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-indigo-700 bg-indigo-50 px-2 py-1 rounded-full">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        {{ $s->teknisi }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Status. Sebelumnya "Proses" berwarna biru di sini,
                                 padahal ungu di Dashboard dan Laporan — komponen
                                 ini menjamin warnanya selalu sama di semua halaman. --}}
                            <td class="px-4 py-4 text-center">
                                <x-status-badge :status="$s->status" />
                            </td>

                            {{-- Aksi. Sebelumnya empat tautan teks polos
                                 ("Detail Invoice Edit Hapus") ditumpuk rapat
                                 tanpa jarak yang jelas, sulit dipindai sekilas
                                 dan mudah salah klik di baris yang padat.
                                 Sekarang tiap aksi jadi tombol ikon berwarna
                                 dengan tooltip, konsisten satu sama lain. --}}
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <div class="aksi-group">
                                    <a href="{{ route('servis.show', $s->id) }}"
                                       class="aksi-btn aksi-btn--blue" title="Lihat detail" aria-label="Lihat detail {{ $s->kode_unik }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('invoice.show', $s->id) }}"
                                       class="aksi-btn aksi-btn--purple" title="Lihat invoice" aria-label="Lihat invoice {{ $s->kode_unik }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <path d="M14 2v6h6"/><path d="M9 15h6"/><path d="M9 11h6"/>
                                        </svg>
                                    </a>

                                    @can('update', $s)
                                        <a href="{{ route('servis.edit', $s->id) }}"
                                           class="aksi-btn aksi-btn--amber" title="Ubah data" aria-label="Ubah data {{ $s->kode_unik }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                                            </svg>
                                        </a>
                                    @endcan

                                    @can('delete', $s)
                                        <form action="{{ route('servis.destroy', $s->id) }}" method="POST" class="inline"
                                              data-konfirmasi="Hapus data servis {{ $s->kode_unik }}? Data masih dapat dipulihkan dari basis data.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="aksi-btn aksi-btn--red" title="Hapus data" aria-label="Hapus data {{ $s->kode_unik }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6"/><path d="M14 11v6"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>

                        </tr>
                    @empty
                        {{-- Dua pesan berbeda untuk dua situasi berbeda. Sebelumnya
                             kedua situasi ini memakai kalimat yang sama — admin yang
                             baru pertama kali memakai sistem dan belum sempat
                             menambah data apa pun tetap dikira "sedang mencari
                             sesuatu", padahal ia tidak mengetik apa pun. --}}
                        @php
                            $adaFilter = filled(request('search')) || filled(request('status'));
                        @endphp
                        <tr>
                            <td colspan="8" class="text-center py-10 text-gray-500">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                     class="mx-auto mb-2 text-gray-300" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <path d="M14 2v6h6"/>
                                </svg>
                                @if($adaFilter)
                                    Tidak ada data servis yang cocok dengan pencarian Anda.
                                    <div class="mt-1">
                                        <a href="{{ route('servis.index') }}" class="text-blue-600 hover:underline text-xs">Hapus pencarian &amp; filter</a>
                                    </div>
                                @else
                                    Belum ada data servis yang tercatat.
                                    @can('create', \App\Modules\Servis\Models\Servis::class)
                                        <div class="mt-1">
                                            <a href="{{ route('servis.create') }}" class="text-blue-600 hover:underline text-xs">Tambah data servis pertama</a>
                                        </div>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6" id="pagination-wrapper">
            {{ $servis->withQueryString()->links() }}
        </div>

    </div>

    @include("servis::partials.live-search")
</x-app-layout>
