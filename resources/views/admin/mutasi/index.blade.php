@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Mutasi Lokasi</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dicatat langsung tanpa approval berlapis</p>
</div>

@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm mb-8">
    <input
        type="text"
        id="search-barang"
        placeholder="Cari nama atau kode barang"
        value="{{ $selectedItem->nama_barang ?? '' }}"
        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-3 mb-2 focus:outline-none focus:ring-2 focus:ring-brand-500"
        autocomplete="off"
    >
    <div id="search-results" class="space-y-2 mb-4"></div>

    <div id="form-mutasi" class="{{ $selectedItem ? '' : 'hidden' }}">
        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
            <p class="text-sm text-gray-500 dark:text-gray-400">Barang dipilih</p>
            <p id="info-nama" class="font-semibold text-gray-800 dark:text-white">{{ $selectedItem->nama_barang ?? '' }}</p>
            <p id="info-kode" class="text-sm text-gray-500 dark:text-gray-400">
                {{ $selectedItem->item_id ?? '' }} &bull; Lokasi saat ini: <span id="info-lokasi">{{ $selectedItem->location->nama_lokasi ?? '' }}</span>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.transaksi.mutasi.store') }}">
            @csrf
            <input type="hidden" name="item_id" id="input-item-id" value="{{ $selectedItem->item_id ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi Asal</label>
                    <input type="text" id="lokasi-asal-display" readonly
                        value="{{ $selectedItem->location->nama_lokasi ?? '' }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400 bg-gray-100 cursor-not-allowed">
                </div>
                <div>
                    <label for="lokasi_tujuan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi Tujuan</label>
                    <select name="lokasi_tujuan_id" id="lokasi_tujuan_id" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">-- Pilih Lokasi Baru --</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan (opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
            </div>

            <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg">
                Catat Mutasi
            </button>
        </form>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
    <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Riwayat Mutasi Terbaru</h3>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Barang</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lokasi Asal &rarr; Tujuan</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($riwayat as $trx)
                <tr>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->item->nama_barang }}</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                        {{ $trx->lokasiAsal->nama_lokasi ?? '-' }} &rarr; {{ $trx->lokasiTujuan->nama_lokasi ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Belum ada riwayat mutasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $riwayat->links() }}</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchUrl = "{{ route('barang.cari') }}";
    const searchInput = document.getElementById('search-barang');
    const searchResults = document.getElementById('search-results');
    const formMutasi = document.getElementById('form-mutasi');
    let debounceTimer;

    function pilihBarang(item) {
        document.getElementById('input-item-id').value = item.item_id;
        document.getElementById('info-nama').textContent = item.nama_barang;
        document.getElementById('info-kode').innerHTML = `${item.item_id} &bull; Lokasi saat ini: <span id="info-lokasi">${item.lokasi}</span>`;
        document.getElementById('lokasi-asal-display').value = item.lokasi;
        formMutasi.classList.remove('hidden');
        searchResults.innerHTML = '';
        searchInput.value = item.nama_barang;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            searchResults.innerHTML = '';
            return;
        }
        debounceTimer = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(items => {
                    searchResults.innerHTML = '';
                    items.forEach(item => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-left border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600';
                        btn.innerHTML = `<span class="font-medium">${item.nama_barang}</span>
                                          <span class="block text-gray-500 dark:text-gray-400 text-sm">${item.item_id} &bull; ${item.lokasi}</span>`;
                        btn.addEventListener('click', () => pilihBarang(item));
                        searchResults.appendChild(btn);
                    });
                });
        }, 300);
    });
});
</script>
@endpush
@endsection