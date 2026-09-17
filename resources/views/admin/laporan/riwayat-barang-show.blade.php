@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
                Riwayat: {{ $item->nama_barang }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $item->item_id }} · {{ $item->category->nama_kategori }} · {{ $item->location->nama_lokasi }}
            </p>
        </div>
        <a href="{{ route('admin.laporan.riwayat-barang.index') }}" class="text-sm text-gray-500 hover:underline dark:text-gray-400">
            ← Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kejadian</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($timeline as $event)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ \Carbon\Carbon::parse($event['tanggal'])->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800 dark:text-gray-200">
                            {{ $event['judul'] }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">
                            {{ $event['oleh'] ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $event['keterangan'] ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            Belum ada riwayat transaksi untuk barang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection