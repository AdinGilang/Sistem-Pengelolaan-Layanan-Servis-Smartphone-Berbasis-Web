<x-app-layout>
    <x-slot name="header">Data Servis</x-slot>
    

    @php $userRole = auth()->user()->role ?? 'admin'; @endphp

    <div class="bg-white shadow-xl rounded-2xl p-6">

        <!-- Top Section -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">

            @if($userRole === 'admin')
                <a href="{{ route('servis.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 transition text-white px-5 py-2 rounded-xl shadow-md inline-flex items-center gap-2 w-fit">
                    + Tambah Servis
                </a>
            @else
                <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:rgba(245,159,0,0.1);border-radius:10px;font-size:13px;color:#d08700;font-weight:500;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Mode tampilan — hanya bisa melihat data
                </div>
            @endif

            {{-- Live Search --}}
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
                    <input
                        id="live-search"
                        type="text"
                        value="{{ request('search') }}"
                        placeholder="Cari pelanggan, kode, merk HP..."
                        class="px-3 py-2 w-72 outline-none bg-transparent text-sm"
                        autocomplete="off"
                    >
                    <button id="clear-search"
                            class="{{ request('search') ? '' : 'hidden' }} pr-3 text-gray-400 hover:text-gray-600"
                            onclick="clearSearch()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                {{-- Hint text --}}
                <div id="search-hint" class="absolute right-0 mt-1 text-xs text-gray-400 hidden">
                    Mengetik...
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-6">
            <form id="filter-form" method="GET" action="{{ route('servis.index') }}" class="flex flex-wrap gap-3 items-center">
                <input type="hidden" name="search" id="filter-search-val" value="{{ request('search') }}">
                <select name="status" id="filter-status" class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Proses"   {{ request('status') == 'Proses'   ? 'selected' : '' }}>Proses</option>
                    <option value="Selesai"  {{ request('status') == 'Selesai'  ? 'selected' : '' }}>Selesai</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                <a href="{{ route('servis.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Reset</a>
                {{-- Showing result info --}}
                @if(request('search'))
                    <span class="text-xs text-gray-500 italic">
                        Hasil pencarian: <strong>"{{ request('search') }}"</strong>
                        — {{ $servis->total() }} data ditemukan
                    </span>
                @endif
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

                            {{-- Status --}}
                            <td class="px-4 py-4 text-center">
                                @if($s->status == 'Menunggu')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">Menunggu</span>
                                @elseif($s->status == 'Proses')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Proses</span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Selesai</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4 text-center space-x-2 whitespace-nowrap">
                                <a href="{{ route('servis.show', $s->id) }}" class="text-blue-600 hover:underline text-xs">Detail</a>
                                <a href="{{ route('invoice.show', $s->id) }}" class="text-purple-600 hover:underline text-xs">Invoice</a>

                                @if($userRole === 'admin')
                                    <a href="{{ route('servis.edit', $s->id) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
                                    <form action="{{ route('servis.destroy', $s->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <div style="font-size:28px;margin-bottom:8px;">📋</div>
                                Data tidak ditemukan.
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

{{-- ── LIVE SEARCH SCRIPT ─────────────────────────────────────────────── --}}
<script>
(function () {
    const input       = document.getElementById('live-search');
    const clearBtn    = document.getElementById('clear-search');
    const icon        = document.getElementById('search-icon');
    const spinner     = document.getElementById('search-spinner');
    const hint        = document.getElementById('search-hint');
    const filterVal   = document.getElementById('filter-search-val');
    const statusSel   = document.getElementById('filter-status');

    let debounceTimer = null;
    let currentSearch = input.value;

    // ── Debounce: tunggu 450ms setelah berhenti mengetik ──────────────
    input.addEventListener('input', function () {
        const val = this.value.trim();

        // Tampil/hide tombol clear
        clearBtn.classList.toggle('hidden', val === '');

        // Tampil hint
        hint.classList.remove('hidden');
        hint.textContent = 'Mengetik...';

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (val === currentSearch) return; // tidak berubah
            currentSearch = val;
            doSearch(val);
        }, 450);
    });

    // ── Enter = langsung cari ─────────────────────────────────────────
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(debounceTimer);
            currentSearch = this.value.trim();
            doSearch(currentSearch);
        }
    });

    // ── Clear button ──────────────────────────────────────────────────
    window.clearSearch = function () {
        input.value   = '';
        currentSearch = '';
        clearBtn.classList.add('hidden');
        doSearch('');
    };

    // ── Fetch & replace tabel ─────────────────────────────────────────
    function doSearch(val) {
        const status = statusSel.value;

        // Show spinner
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
        hint.textContent = 'Mencari...';

        // Build URL
        const url = new URL(window.location.href);
        url.searchParams.set('search', val);
        url.searchParams.set('status', status);
        url.searchParams.set('page', 1);

        // Update browser URL tanpa reload
        window.history.replaceState({}, '', url.toString());

        // Sync hidden input di filter form
        filterVal.value = val;

        // Fetch HTML
        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc    = parser.parseFromString(html, 'text/html');

                // Ganti tbody
                const newBody = doc.getElementById('table-body');
                if (newBody) {
                    document.getElementById('table-body').innerHTML = newBody.innerHTML;
                }

                // Ganti pagination
                const newPag = doc.getElementById('pagination-wrapper');
                if (newPag) {
                    document.getElementById('pagination-wrapper').innerHTML = newPag.innerHTML;
                }

                // Update result info
                const newFilter = doc.getElementById('filter-form');
                if (newFilter) {
                    const info = newFilter.querySelector('span.italic');
                    const oldInfo = document.querySelector('#filter-form span.italic');
                    if (info && oldInfo)  oldInfo.outerHTML = info.outerHTML;
                    else if (!info && oldInfo) oldInfo.remove();
                    else if (info && !oldInfo) {
                        document.getElementById('filter-form').appendChild(info.cloneNode(true));
                    }
                }

                // Restore icon
                icon.classList.remove('hidden');
                spinner.classList.add('hidden');
                hint.classList.add('hidden');
            })
            .catch(() => {
                icon.classList.remove('hidden');
                spinner.classList.add('hidden');
                hint.classList.add('hidden');
            });
    }

    // ── Filter status juga pakai live search ──────────────────────────
    statusSel.addEventListener('change', function () {
        doSearch(input.value.trim());
    });
})();
</script>

</x-app-layout>