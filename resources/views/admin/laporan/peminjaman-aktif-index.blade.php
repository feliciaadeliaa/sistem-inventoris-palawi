@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Laporan dan Ekspor
        </h2>
    </div>

    @include('admin.laporan._tabs', ['active' => 'peminjaman-aktif'])

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.laporan.peminjaman-aktif.index') }}" class="mb-4 bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status_pinjam" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    <option value="aktif" {{ request('status_pinjam') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="terlambat" {{ request('status_pinjam') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
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
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded">
                    Terapkan
                </button>
                @if (request()->anyFilled(['status_pinjam', 'location_id', 'category_id']))
                    <a href="{{ route('admin.laporan.peminjaman-aktif.index') }}" class="text-sm text-gray-500 hover:underline dark:text-gray-400">
                        Reset filter
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.peminjaman-aktif.export-pdf', request()->query()) }}" target="_blank"
                    class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded">
                    Ekspor PDF
                </a>
                <a href="{{ route('admin.laporan.peminjaman-aktif.export-excel', request()->query()) }}"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded">
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dipinjam Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tgl Disetujui</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estimasi Kembali</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($transactions as $trx)
                    @php
                        $terlambat = $trx->tanggal_kembali_estimasi && $trx->tanggal_kembali_estimasi->isPast();
                    @endphp
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $trx->item->nama_barang ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $trx->user->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $trx->approved_at?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $trx->tanggal_kembali_estimasi?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $terlambat ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $terlambat ? 'Terlambat' : 'Aktif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada barang yang sedang dipinjam.
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