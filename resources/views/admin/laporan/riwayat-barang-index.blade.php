@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Laporan dan Ekspor
        </h2>
    </div>

    {{-- Tab Navigation --}}
     @include('admin.laporan._tabs', ['active' => 'riwayat-barang'])

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.laporan.riwayat-barang.index') }}" class="mb-4 bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lokasi</label>
                <select name="location_id" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kategori</label>
                <select name="category_id" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jenis Transaksi</label>
                <select name="jenis_transaksi" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua Jenis --</option>
                    @foreach ($jenisOptions as $jenis)
                        <option value="{{ $jenis }}" {{ request('jenis_transaksi') == $jenis ? 'selected' : '' }}>
                            {{ $jenis }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded">
                    Terapkan
                </button>
                @if (request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'location_id', 'category_id', 'jenis_transaksi']))
                    <a href="{{ route('admin.laporan.riwayat-barang.index') }}" class="text-sm text-gray-500 hover:underline dark:text-gray-400">
                        Reset filter
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.riwayat-barang.export-pdf', request()->query()) }}" target="_blank"
                    class="flex items-center gap-1.5 bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded">
                    Ekspor PDF
                </a>
                <a href="{{ route('admin.laporan.riwayat-barang.export-excel', request()->query()) }}"
                    class="flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded">
                    Ekspor Excel
                </a>
            </div>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pemohon</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $trx->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($trx->item)
                                <a href="{{ route('admin.laporan.riwayat-barang.show', $trx->item) }}" class="text-brand-500 hover:underline">
                                    {{ $trx->item->nama_barang }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $trx->jenis_transaksi }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                {{ $statusLabels[$trx->status] ?? ucfirst($trx->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $trx->user->name ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data transaksi yang cocok dengan filter.
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