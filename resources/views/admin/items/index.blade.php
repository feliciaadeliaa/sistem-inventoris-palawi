@extends('layouts.app')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Master Data Barang') }}
        </h2>
        <a href="{{ route('barang.create') }}"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
            + {{ __('Tambah Barang') }}
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Item ID') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Nama Barang') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Kategori') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Lokasi') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Golongan AT') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Tahun Perolehan') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Masa Manfaat') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Nilai Perolehan') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Tanggal Terima') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Kondisi') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Status') }}</th>
                    <th class="px-4 py-3 whitespace-nowrap">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-white/90">{{ $item->item_id }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-white/90">{{ $item->nama_barang }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->category->nama_kategori }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->location->nama_lokasi }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->golongan_at }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->tahun_perolehan }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->masa_manfaat }} {{ __('tahun') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">Rp {{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->tanggal_terima->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap capitalize text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', $item->kondisi) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap capitalize text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', $item->status) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('barang.edit', $item) }}" class="text-brand-500 hover:underline">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('Belum ada data barang.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
@endsection