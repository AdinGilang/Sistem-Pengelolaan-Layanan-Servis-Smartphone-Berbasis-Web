<x-layouts.public
    title="Syarat & Ketentuan"
    description="Ketentuan layanan servis Phone Repair: garansi, batas waktu pengambilan perangkat, estimasi biaya, dan tanggung jawab atas data pada perangkat."
>
    <article class="prose-id">
        <h1>Syarat &amp; Ketentuan Layanan</h1>
        <p class="prose-id__meta">Berlaku sejak {{ now()->translatedFormat('d F Y') }}</p>

        <p>
            Dengan menyerahkan perangkat untuk diservis di Phone Repair, Anda menyetujui ketentuan
            berikut. Ketentuan ini juga tercetak ringkas pada nota servis yang Anda terima.
        </p>

        <h2>Penerimaan perangkat</h2>
        <ul>
            <li>Setiap perangkat yang masuk menerima satu kode servis unik sebagai bukti penerimaan.</li>
            <li>Kelengkapan yang dititipkan dicatat pada nota. Barang di luar catatan tersebut dianggap tidak dititipkan.</li>
            <li>Simpan kode servis Anda; kode itu dipakai untuk melacak status maupun mengambil perangkat.</li>
        </ul>

        <h2>Estimasi dan biaya</h2>
        <ul>
            <li>Estimasi waktu pengerjaan bersifat perkiraan dan dapat berubah bila ditemukan kerusakan lanjutan.</li>
            <li>Perubahan biaya di luar estimasi awal dikomunikasikan lebih dulu sebelum pengerjaan dilanjutkan.</li>
            <li>Biaya akhir tertera pada invoice servis yang diterbitkan saat perangkat diambil.</li>
        </ul>

        <h2>Garansi</h2>
        <p>
            Garansi berlaku atas komponen yang diganti dan pengerjaan yang dilakukan, sesuai masa yang
            tercantum pada nota servis Anda. Garansi gugur apabila perangkat terkena cairan, jatuh,
            dibongkar pihak lain, atau tersegel rusak setelah keluar dari gerai kami.
        </p>

        <h2>Batas waktu pengambilan</h2>
        <p>
            Perangkat yang telah selesai wajib diambil sesuai batas waktu yang tertera pada nota.
            Perangkat yang tidak diambil melewati batas tersebut berada di luar tanggung jawab kami.
        </p>

        <h2>Data pada perangkat</h2>
        <ul>
            <li>Kami menyarankan Anda mencadangkan data pribadi sebelum menyerahkan perangkat.</li>
            <li>Sebagian perbaikan, misalnya penggantian papan induk atau pemasangan ulang perangkat lunak, dapat menghapus seluruh data.</li>
            <li>Pola kunci atau PIN yang Anda titipkan hanya dipakai untuk menguji hasil perbaikan.</li>
        </ul>

        <h2>Pelacakan status</h2>
        <p>
            Status perbaikan dapat dipantau kapan saja melalui halaman
            <a href="{{ route('servis.cek') }}">Cek Status Servis</a> dengan memasukkan kode servis Anda.
        </p>
    </article>
</x-layouts.public>
