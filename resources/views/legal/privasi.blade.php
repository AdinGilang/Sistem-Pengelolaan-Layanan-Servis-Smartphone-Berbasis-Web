<x-layouts.public
    title="Kebijakan Privasi"
    description="Penjelasan data apa saja yang dikumpulkan Phone Repair saat Anda menyervis perangkat, bagaimana data itu disimpan, dan hak Anda atas data tersebut."
>
    <article class="prose-id">
        <h1>Kebijakan Privasi</h1>
        <p class="prose-id__meta">Berlaku sejak {{ now()->translatedFormat('d F Y') }}</p>

        <p>
            Halaman ini menjelaskan data apa saja yang kami catat ketika Anda menyervis perangkat di
            Phone Repair, untuk apa data tersebut dipakai, dan bagaimana data itu kami jaga.
        </p>

        <h2>Data yang kami kumpulkan</h2>
        <ul>
            <li>Nama dan nomor WhatsApp, untuk menghubungi Anda saat perbaikan selesai.</li>
            <li>Alamat, hanya jika Anda meminta layanan antar atau jemput perangkat.</li>
            <li>Merek, tipe, kelengkapan, dan keluhan kerusakan perangkat yang diservis.</li>
            <li>Pola kunci atau PIN perangkat, hanya jika teknisi memang memerlukannya untuk menguji hasil perbaikan.</li>
        </ul>

        <h2>Bagaimana data dijaga</h2>
        <ul>
            <li>PIN perangkat disimpan sebagai hash satu arah, sehingga tidak dapat dibaca kembali oleh siapa pun, termasuk oleh staf kami.</li>
            <li>Pola kunci disimpan dalam keadaan terenkripsi dan hanya dapat dibuka oleh sistem saat dibutuhkan teknisi.</li>
            <li>Akses ke data servis dibatasi berdasarkan peran. Tidak ada akun yang bisa mendaftar sendiri dari luar.</li>
            <li>Seluruh kata sandi akun staf disimpan dalam bentuk hash, bukan teks biasa.</li>
        </ul>

        <h2>Halaman pelacakan status</h2>
        <p>
            Halaman <a href="{{ route('servis.cek') }}">Cek Status Servis</a> hanya menampilkan
            informasi yang perlu Anda ketahui: kode servis, perangkat, keluhan, teknisi yang menangani,
            status, dan biaya. Alamat, nomor WhatsApp, pola kunci, dan PIN tidak pernah ditampilkan
            di halaman publik ini.
        </p>

        <h2>Berapa lama data disimpan</h2>
        <p>
            Catatan servis disimpan sebagai arsip transaksi. Kredensial perangkat (pola kunci dan PIN)
            dihapus atas permintaan Anda setelah perangkat diambil.
        </p>

        <h2>Hak Anda</h2>
        <ul>
            <li>Meminta salinan catatan servis atas nama Anda.</li>
            <li>Meminta koreksi data yang keliru.</li>
            <li>Meminta penghapusan kredensial perangkat setelah perbaikan selesai.</li>
        </ul>

        <h2>Menghubungi kami</h2>
        <p>
            Untuk pertanyaan mengenai data Anda, silakan hubungi staf Phone Repair secara langsung
            di gerai atau melalui nomor kontak yang tertera pada nota servis Anda.
        </p>
    </article>
</x-layouts.public>
