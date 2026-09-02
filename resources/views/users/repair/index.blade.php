@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Riwayat Perbaikan Saya
        </h2>

        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Lihat riwayat pengajuan perbaikan barang Anda.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4 flex justify-end">
        <button
            type="button"
            id="btn-toggle-ajukan"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
        >
            + Ajukan Perbaikan Baru
        </button>
    </div>

    {{-- Panel cari barang (alternatif scan) --}}
    <div id="panel-ajukan" class="hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm mb-4">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
            Cari barang yang ingin dilaporkan rusak/perlu diperbaiki, atau
            <a href="{{ route('barang.scan') }}" class="text-indigo-600 dark:text-indigo-400 underline">scan QR barang</a> sebagai alternatif.
        </p>
        <input
            type="text"
            id="search-barang"
            placeholder="Cari nama atau kode barang"
            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-800 dark:text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-600"
            autocomplete="off"
        >
        <div id="search-results" class="mt-3 space-y-2"></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Barang
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Keterangan
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Status
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Diajukan Pada
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                @forelse ($transactions as $trx)

                    <tr>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $trx->item->nama_barang ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $trx->keterangan }}
                        </td>

                        <td class="px-4 py-3">

                            <span @class([
                                'px-2 py-1 rounded text-xs font-semibold',

                                'bg-yellow-100 text-yellow-800' =>
                                    $trx->status === 'menunggu_approval',

                                'bg-blue-100 text-blue-800' =>
                                    in_array($trx->status, ['diproses', 'dalam_perbaikan']),

                                'bg-green-100 text-green-800' =>
                                    $trx->status === 'selesai_berhasil',

                                'bg-red-100 text-red-800' =>
                                    $trx->status === 'selesai_gagal',
                            ])>

                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}

                            </span>

                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $trx->created_at->format('d M Y') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4"
                            class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">

                            Belum ada pengajuan perbaikan.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchUrl = "{{ route('barang.cari') }}";
    const createUrl = "{{ route('perbaikan.create') }}";

    const btnToggle = document.getElementById('btn-toggle-ajukan');
    const panel = document.getElementById('panel-ajukan');
    const searchInput = document.getElementById('search-barang');
    const searchResults = document.getElementById('search-results');

    btnToggle.addEventListener('click', function () {
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            searchInput.focus();
        }
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

                    if (items.length === 0) {
                        searchResults.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">Barang tidak ditemukan.</p>';
                        return;
                    }

                    items.forEach(item => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-left bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 text-gray-800 dark:text-white rounded-lg px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-800';
                        btn.innerHTML = `<span class="font-medium">${item.nama_barang}</span>
                                          <span class="block text-gray-500 dark:text-gray-400 text-sm">${item.item_id} · ${item.lokasi}</span>`;
                        btn.addEventListener('click', () => {
                            window.location.href = `${createUrl}?item_id=${encodeURIComponent(item.item_id)}`;
                        });
                        searchResults.appendChild(btn);
                    });
                })
                .catch(() => {
                    searchResults.innerHTML = '<p class="text-sm text-red-600 dark:text-red-400">Gagal mencari barang.</p>';
                });
        }, 300);
    });
});
</script>
@endpush