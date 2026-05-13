<x-app-layout>
    <x-slot name="header">Pengaturan Invoice</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-2xl p-8">

            <div style="border-left:4px solid #3b5bdb;padding-left:12px;margin-bottom:24px;">
                <div class="font-bold text-gray-800">Teks Footer Invoice</div>
                <div class="text-xs text-gray-400 mt-1">Teks ini akan muncul di bagian bawah invoice cetak & PDF</div>
            </div>

            @if(session('success'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('setting.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Ucapan Terima Kasih
                    </label>
                    <input type="text" name="footer_thanks"
                           value="{{ old('footer_thanks', $settings['footer_thanks']) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Terima kasih atas kepercayaan Anda.">
                    @error('footer_thanks')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Garansi Servis
                    </label>
                    <input type="text" name="garansi_servis"
                           value="{{ old('garansi_servis', $settings['garansi_servis']) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Garansi servis 7 hari setelah pengambilan.">
                    @error('garansi_servis')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Contoh: Garansi servis 14 hari setelah pengambilan.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Batas Pengambilan
                    </label>
                    <input type="text" name="batas_pengambilan"
                           value="{{ old('batas_pengambilan', $settings['batas_pengambilan']) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Batas Pengambilan Maksimal 3 Bulan!">
                    @error('batas_pengambilan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Contoh: Batas Pengambilan Maksimal 1 Bulan!</p>
                </div>

                {{-- Preview --}}
                <div style="background:#f8f9ff;border:1px dashed #bbb;border-radius:10px;padding:16px;">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Preview Footer Invoice</div>
                    <div style="width:160px;margin:0 auto;border:1px solid #eee;border-radius:4px;padding:10px;background:#fff;font-size:7.5px;color:#777;text-align:center;line-height:1.8;">
                        <div id="prev_thanks"    style="margin-bottom:2px;">{{ $settings['footer_thanks'] }}</div>
                        <div id="prev_garansi"   style="margin-bottom:2px;">{{ $settings['garansi_servis'] }}</div>
                        <div id="prev_batas"     style="font-weight:bold;color:#555;">{{ $settings['batas_pengambilan'] }}</div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        💾 Simpan Pengaturan
                    </button>
                </div>

            </form>
        </div>
    </div>

<script>
// Live preview
function livePreview(inputId, previewId) {
    const input   = document.querySelector(`[name="${inputId}"]`);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    input.addEventListener('input', () => preview.textContent = input.value || '—');
}
livePreview('footer_thanks',     'prev_thanks');
livePreview('garansi_servis',    'prev_garansi');
livePreview('batas_pengambilan', 'prev_batas');
</script>

</x-app-layout>