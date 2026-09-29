@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <h2 class="page-title">Ajukan Peminjaman</h2>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-success-50 text-success-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-error-50 text-error-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-error-50 text-error-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-pad">
        <form method="POST" action="{{ route('peminjaman.store') }}">
            @csrf

            {{-- Pilih Barang --}}
            <label class="form-label">Barang</label>

            <div id="item-selected" class="{{ $item ? '' : 'hidden' }} border border-gray-200 rounded-lg px-4 py-3 mb-3 bg-brand-50/50 flex items-center justify-between">
                <div>
                    <p id="item-selected-nama" class="text-gray-800 font-medium">{{ $item->nama_barang ?? '' }}</p>
                    <p id="item-selected-info" class="text-gray-500 text-sm">
                        {{ $item->item_id ?? '' }} &middot; {{ $item->location->nama_lokasi ?? '' }}
                    </p>
                </div>
            </div>

            <div id="item-search-wrapper" class="{{ $item ? 'hidden' : '' }}">
                <input
                    type="text"
                    id="search-barang"
                    placeholder="Cari nama atau kode barang"
                    class="form-control h-auto px-4 py-3 mb-2"
                    autocomplete="off"
                >
                <div id="search-results" class="space-y-2 mb-3"></div>
            </div>

            <input type="hidden" name="item_id" id="item_id" value="{{ old('item_id', $item->item_id ?? '') }}">

            {{-- Tanggal Kembali --}}
            <label for="tanggal_kembali_estimasi" class="form-label mt-4">
                Perkiraan Tanggal Kembali
            </label>
            <input
                type="date"
                name="tanggal_kembali_estimasi"
                id="tanggal_kembali_estimasi"
                value="{{ old('tanggal_kembali_estimasi') }}"
                min="{{ now()->format('Y-m-d') }}"
                class="form-control mb-4"
                required
            >

            {{-- Keterangan --}}
            <label for="keterangan" class="form-label">
                Keterangan (opsional)
            </label>
            <textarea
                name="keterangan"
                id="keterangan"
                rows="3"
                placeholder="Contoh: dipakai untuk presentasi klien tanggal 5 Sept"
                class="form-control h-auto py-2 mb-6"
            >{{ old('keterangan') }}</textarea>

            <button
                type="submit"
                id="submit-btn"
                class="btn btn-primary w-full"
            >
                Kirim Pengajuan
            </button>
        </form>
    </div>
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
    const submitButton = document.getElementById('submit-btn');

    function pilihBarang(item) {
        itemIdInput.value = item.item_id;
        itemSelectedNama.textContent = item.nama_barang;

        if (item.sedang_diajukan) {
            itemSelectedInfo.innerHTML = `${item.item_id} &middot; ${item.lokasi} <span class="text-warning-600 font-semibold">(Sedang diajukan pengguna lain)</span>`;
        } else {
            itemSelectedInfo.textContent = `${item.item_id} · ${item.lokasi}`;
        }

        itemSelected.classList.remove('hidden');
        itemSearchWrapper.classList.add('hidden');
        searchResults.innerHTML = '';
        searchInput.value = '';

        submitButton.disabled = item.sedang_diajukan;
        submitButton.classList.toggle('opacity-50', item.sedang_diajukan);
        submitButton.classList.toggle('cursor-not-allowed', item.sedang_diajukan);
    }

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
                            btn.className = 'w-full text-left border border-gray-300 bg-white text-gray-800 rounded-lg px-4 py-3 hover:bg-gray-50';

                            const badge = item.sedang_diajukan
                                ? '<span class="ml-2 text-xs bg-warning-100 text-warning-700 px-2 py-0.5 rounded">Sedang diajukan</span>'
                                : '';

                            btn.innerHTML = `<span class="font-medium">${item.nama_barang}</span>${badge}
                                              <span class="block text-gray-500 text-sm">${item.item_id} &middot; ${item.lokasi}</span>`;
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