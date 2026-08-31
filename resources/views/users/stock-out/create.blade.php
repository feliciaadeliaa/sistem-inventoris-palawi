<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ajukan Peminjaman Barang
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6 p-4 bg-gray-50 rounded border">
                    <p class="text-sm text-gray-500">Barang yang akan dipinjam</p>
                    <p class="font-semibold text-lg">{{ $item->nama_barang }}</p>
                    <p class="text-sm text-gray-500">Kode: {{ $item->item_id }} &bull; Lokasi: {{ $item->location->nama_lokasi ?? '-' }}</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
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
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">
                            Tujuan Peminjaman
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('keterangan') }}</textarea>
                    </div>

                    <div>
                        <label for="tanggal_kembali_estimasi" class="block text-sm font-medium text-gray-700">
                            Estimasi Tanggal Kembali
                        </label>
                        <input type="date" name="tanggal_kembali_estimasi" id="tanggal_kembali_estimasi" required
                            min="{{ now()->format('Y-m-d') }}"
                            value="{{ old('tanggal_kembali_estimasi') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 text-gray-600">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Ajukan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>