<h1 class="text-2xl font-bold mb-6">
Dashboard Admin
</h1>

<div class="grid grid-cols-4 gap-4 mb-6">

<div class="bg-blue-500 text-white p-4 rounded">
<h2>Total Servis</h2>
<p class="text-2xl font-bold">{{ $total }}</p>
</div>

<div class="bg-yellow-500 text-white p-4 rounded">
<h2>Menunggu</h2>
<p class="text-2xl font-bold">{{ $menunggu }}</p>
</div>

<div class="bg-purple-500 text-white p-4 rounded">
<h2>Proses</h2>
<p class="text-2xl font-bold">{{ $proses }}</p>
</div>

<div class="bg-green-500 text-white p-4 rounded">
<h2>Selesai</h2>
<p class="text-2xl font-bold">{{ $selesai }}</p>
</div>

</div>

<a href="{{ route('servis.index') }}"
class="bg-gray-800 text-white px-4 py-2 rounded">
Kelola Data Servis
</a>
