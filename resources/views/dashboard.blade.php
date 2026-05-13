<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    @php
        $total    = \App\Models\Servis::count();
        $menunggu = \App\Models\Servis::where('status','Menunggu')->count();
        $proses   = \App\Models\Servis::where('status','Proses')->count();
        $selesai  = \App\Models\Servis::where('status','Selesai')->count();
        $pct      = fn($n) => $total > 0 ? round(($n / $total) * 100) : 0;
        $recentServis = \App\Models\Servis::latest()->take(5)->get();
    @endphp

    {{-- ── STAT CARDS ── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px;">

        {{-- Total Servis --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 32px rgba(26,31,54,.12)'"
             onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(26,31,54,0.08)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#3b5bdb;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(59,91,219,0.1);color:#3b5bdb;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div style="font-size:12px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Total Servis</div>
            <div style="font-size:36px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $total }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">Semua data servis</div>
        </div>

        {{-- Menunggu --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 32px rgba(26,31,54,.12)'"
             onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(26,31,54,0.08)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#f59f00;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(245,159,0,0.1);color:#f59f00;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div style="font-size:12px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Menunggu</div>
            <div style="font-size:36px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $menunggu }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">Belum ditangani</div>
        </div>

        {{-- Proses --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 32px rgba(26,31,54,.12)'"
             onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(26,31,54,0.08)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#9c36b5;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(156,54,181,0.1);color:#9c36b5;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <div style="font-size:12px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Proses</div>
            <div style="font-size:36px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $proses }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">Sedang dikerjakan</div>
        </div>

        {{-- Selesai --}}
        <div style="background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 4px 24px rgba(26,31,54,0.08);position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 32px rgba(26,31,54,.12)'"
             onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(26,31,54,0.08)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:#2f9e44;border-radius:14px 14px 0 0;"></div>
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(47,158,68,0.1);color:#2f9e44;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div style="font-size:12px;font-weight:600;color:#8a93b2;text-transform:uppercase;letter-spacing:.5px;">Selesai</div>
            <div style="font-size:36px;font-weight:700;color:#1a1f36;line-height:1.1;margin-top:4px;letter-spacing:-1px;">{{ $selesai }}</div>
            <div style="font-size:12px;color:#8a93b2;margin-top:6px;">Telah diselesaikan</div>
        </div>

    </div>

    {{-- ── BOTTOM ROW ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

        {{-- Data Servis Terbaru --}}
        <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#1a1f36;">Data Servis Terbaru</div>
                    <div style="font-size:12px;color:#8a93b2;margin-top:1px;">{{ $total }} total entri servis</div>
                </div>
                <a href="{{ route('servis.index') }}"
                   style="display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:10px;font-size:12px;font-weight:600;background:#3b5bdb;color:#fff;text-decoration:none;box-shadow:0 4px 12px rgba(59,91,219,0.3);transition:all .18s;line-height:1;"
                   onmouseover="this.style.background='#3451c7';this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.background='#3b5bdb';this.style.transform=''">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Kelola Data
                </a>
            </div>
            <div style="padding:6px 22px;">
                @php $recentServis = \App\Models\Servis::latest()->take(5)->get(); @endphp
                @forelse($recentServis as $item)
                    @php
                        $statusStyle = match($item->status) {
                            'Menunggu' => 'background:rgba(245,159,0,0.12);color:#d08700;',
                            'Proses'   => 'background:rgba(156,54,181,0.12);color:#8a2be2;',
                            'Selesai'  => 'background:rgba(47,158,68,0.12);color:#2f9e44;',
                            default    => 'background:#f0f2f7;color:#8a93b2;',
                        };
                        // ✅ kolom yang benar adalah 'pelanggan'
                        $initial = strtoupper(substr($item->pelanggan ?? 'S', 0, 1));
                    @endphp
                    <div class="flex items-center gap-4 py-3" style="border-bottom:1px solid #f4f5f9;">
                        {{-- Avatar inisial dari nama pelanggan --}}
                        <div class="flex items-center justify-center rounded-xl flex-shrink-0"
                             style="width:38px;height:38px;background:#f0f2f7;font-size:18px;">
                            📱
                        </div>
                        <div class="min-w-0 flex-1">
                            <div style="font-size:13px;font-weight:600;color:#1a1f36;">
                                {{ $item->pelanggan ?? '-' }}
                            </div>
                            <div style="font-size:12px;color:#8a93b2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $item->kerusakan ?? '-' }}
                            </div>
                        </div>
                        <span class="rounded-full font-semibold flex-shrink-0"
                              style="font-size:11px;padding:4px 10px;white-space:nowrap;{{ $statusStyle }}">
                            {{ $item->status }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center" style="color:#8a93b2;font-size:14px;">
                        <div style="font-size:28px;margin-bottom:8px;">📋</div>
                        Belum ada data servis.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Ringkasan Status --}}
        <div style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,31,54,0.08);overflow:hidden;">
            <div style="padding:18px 22px 14px;border-bottom:1px solid #f0f2f7;">
                <div style="font-size:14px;font-weight:700;color:#1a1f36;">Ringkasan Status</div>
                <div style="font-size:12px;color:#8a93b2;margin-top:1px;">Distribusi pekerjaan saat ini</div>
            </div>
            <div style="padding:16px 22px;">

                {{-- Total --}}
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;color:#1a1f36;">Total Servis</span>
                        <span style="font-size:12px;font-weight:700;color:#3b5bdb;">{{ $total }} unit</span>
                    </div>
                    <div style="height:8px;background:#f0f2f7;border-radius:99px;overflow:hidden;">
                        <div style="width:100%;height:100%;background:#3b5bdb;border-radius:99px;"></div>
                    </div>
                </div>

                {{-- Menunggu --}}
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;color:#1a1f36;">Menunggu</span>
                        <span style="font-size:12px;font-weight:700;color:#f59f00;">{{ $pct($menunggu) }}% ({{ $menunggu }} unit)</span>
                    </div>
                    <div style="height:8px;background:#f0f2f7;border-radius:99px;overflow:hidden;">
                        <div style="width:{{ $pct($menunggu) }}%;height:100%;background:#f59f00;border-radius:99px;"></div>
                    </div>
                </div>

                {{-- Proses --}}
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;color:#1a1f36;">Sedang Diproses</span>
                        <span style="font-size:12px;font-weight:700;color:#9c36b5;">{{ $pct($proses) }}% ({{ $proses }} unit)</span>
                    </div>
                    <div style="height:8px;background:#f0f2f7;border-radius:99px;overflow:hidden;">
                        <div style="width:{{ $pct($proses) }}%;height:100%;background:#9c36b5;border-radius:99px;"></div>
                    </div>
                </div>

                {{-- Selesai --}}
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;color:#1a1f36;">Selesai</span>
                        <span style="font-size:12px;font-weight:700;color:#2f9e44;">{{ $pct($selesai) }}% ({{ $selesai }} unit)</span>
                    </div>
                    <div style="height:8px;background:#f0f2f7;border-radius:99px;overflow:hidden;">
                        <div style="width:{{ $pct($selesai) }}%;height:100%;background:#2f9e44;border-radius:99px;"></div>
                    </div>
                </div>

                {{-- Info box --}}
                <div style="margin-top:20px;padding:14px;background:#f7f8fc;border-radius:10px;display:flex;align-items:center;gap:10px;">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(59,91,219,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#1a1f36;">Tingkat Penyelesaian</div>
                        <div style="font-size:12px;color:#8a93b2;">
                            {{ $selesai }} dari {{ $total }} servis telah selesai.
                            @if($selesai < $total) Segera tindak lanjuti! @else Semua servis selesai! 🎉 @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</x-app-layout>