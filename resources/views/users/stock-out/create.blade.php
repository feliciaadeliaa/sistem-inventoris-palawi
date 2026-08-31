@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-semibold text-white mb-6">Ajukan Peminjaman</h1>

    @if (session('success'))
        <div class="bg-green-900/40 border border-green-700 text-green-300 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-900/40 border border-red-700 text-red-300 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('peminjaman.store') }}">
        @csrf

        {{-- Pilih Barang --}}
        <label class="block text-gray-300 text-sm mb-2">Barang</label>

        <div id="item-selected" class="{{ $item ? '' : 'hidden' }} border border-gray-700 rounded-lg px-4 py-3 mb-3 bg-gray-900/40 flex items-center justify-between">
            <div>
                <p id="item-selected-nama" class="text-white font-medium">{{ $item->nama_barang ?? '' }}</p>
                <p id="item-selected-info" class="text-gray-400 text-sm">
                    {{ $item->item_id ?? '' }} · {{ $item->location->nama_lokasi ?? '' }}
                </p>
            </div>
            <button type="button" id="btn-ganti-barang" class="text-sm text-green-500 underline">
                Ganti
            </button>
        </div>

        <div id="item-search-wrapper" class="{{ $item ? 'hidden' : '' }}">
            <input
                type="text"
                id="search-barang"
                placeholder="Cari nama atau kode barang"
                class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 mb-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                autocomplete="off"
            >
            <div id="search-results" class="space-y-2 mb-3"></div>
        </div>

        <input type="hidden" name="item_id" id="item_id" value="{{ old('item_id', $item->item_id ?? '') }}">

        {{-- Tanggal Kembali --}}
        <label for="tanggal_kembali_estimasi" class="block text-gray-300 text-sm mb-2 mt-4">
            Perkiraan Tanggal Kembali
        </label>
        <input
            type="date"
            name="tanggal_kembali_estimasi"
            id="tanggal_kembali_estimasi"
            value="{{ old('tanggal_kembali_estimasi') }}"
            min="{{ now()->format('Y-m-d') }}"
            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 mb-4 focus:outline-none focus:ring-2 focus:ring-green-600"
            required
        >

        {{-- Keterangan --}}
        <label for="keterangan" class="block text-gray-300 text-sm mb-2">
            Keterangan (opsional)
        </label>
        <textarea
            name="keterangan"
            id="keterangan"
            rows="3"
            placeholder="Contoh: dipakai untuk presentasi klien tanggal 5 Sept"
            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 mb-6 focus:outline-none focus:ring-2 focus:ring-green-600"
        >{{ old('keterangan') }}</textarea>

        <button
            type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg px-4 py-3"
        >
            Kirim Pengajuan
        </button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchUrl = "{{ route('barang.cari') }}";

    const itemIdInput = document.getElementById('item_id');
    const itemSelected = document.getElementById('item-selected');
    const itemSelectedNama = document.getElementById('item-selected-nama');
    const itemSelectedInfo = document.getElementById('item-selected-info');
    const itemSearchWrapper = document.getElementById('item-search-wrapper');
    const searchInput = document.getElementById('search-barang');
    const searchResults = document.getElementById('search-results');

    function pilihBarang(item) {
        itemIdInput.value = item.item_id;
        itemSelectedNama.textContent = item.nama_barang;
        itemSelectedInfo.textContent = `${item.item_id} · ${item.lokasi}`;
        itemSelected.classList.remove('hidden');
        itemSearchWrapper.classList.add('hidden');
        searchResults.innerHTML = '';
        searchInput.value = '';
    }

    document.getElementById('btn-ganti-barang').addEventListener('click', function () {
        itemSelected.classList.add('hidden');
        itemSearchWrapper.classList.remove('hidden');
        itemIdInput.value = '';
    });

    let debounceTimer;
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
                    items
                        .filter(item => item.status === 'tersedia')
                        .forEach(item => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'w-full text-left bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 hover:bg-gray-800';
                            btn.innerHTML = `<span class="font-medium">${item.nama_barang}</span>
                                              <span class="block text-gray-400 text-sm">${item.item_id} · ${item.lokasi}</span>`;
                            btn.addEventListener('click', () => pilihBarang(item));
                            searchResults.appendChild(btn);
                        });

                    if (searchResults.children.length === 0) {
                        searchResults.innerHTML = '<p class="text-gray-500 text-sm">Barang tidak ditemukan atau sedang tidak tersedia.</p>';
                    }
                });
        }, 300);
    });
});
</script>
@endpush
@endsection