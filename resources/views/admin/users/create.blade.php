@extends('layouts.app')

@section('content')
    <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Ajukan Peminjaman Barang</h2>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">

            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                <p class="text-sm text-gray-500 dark:text-gray-400">Barang yang akan dipinjam</p>
                <p class="font-semibold text-lg text-gray-800 dark:text-white">{{ $item->nama_barang }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Kode: {{ $item->item_id }} &bull; Lokasi: {{ $item->location->nama_lokasi ?? '-' }}
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('peminjaman.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->item_id }}">

                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tujuan Peminjaman
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3" required
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">{{ old('keterangan') }}</textarea>
                </div>

                <div>
                    <label for="tanggal_kembali_estimasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Estimasi Tanggal Kembali
                    </label>
                    <input type="date" name="tanggal_kembali_estimasi" id="tanggal_kembali_estimasi" required
                        min="{{ now()->format('Y-m-d') }}"
                        value="{{ old('tanggal_kembali_estimasi') }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-300">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Ajukan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection