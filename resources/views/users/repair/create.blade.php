@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-semibold text-white mb-2">Ajukan perbaikan</h1>
    <p class="text-gray-400 text-sm mb-6">Isi keterangan kerusakan untuk barang berikut, lalu ajukan.</p>

    <div class="border border-gray-700 rounded-xl p-5 bg-gray-900/40 mb-6">
        <p class="text-gray-400 text-sm mb-1">Barang</p>
        <h2 class="text-white text-lg font-semibold">{{ $item->nama_barang }}</h2>
        {{-- TODO: ganti 'nama_lokasi' di bawah kalau field di model Location namanya beda --}}
        <p class="text-gray-400 text-sm">{{ $item->item_id }} · {{ $item->location?->nama_lokasi ?? '—' }}</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-700 bg-red-900/30 text-red-300 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('perbaikan.store') }}">
        @csrf
        <input type="hidden" name="item_id" value="{{ $item->item_id }}">

        <label for="keterangan" class="block text-gray-300 text-sm mb-2">Keterangan kerusakan</label>
        <textarea
            id="keterangan"
            name="keterangan"
            rows="5"
            maxlength="1000"
            placeholder="Jelaskan kerusakan yang terjadi pada barang ini..."
            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 mb-6 focus:outline-none focus:ring-2 focus:ring-green-600"
            required
        >{{ old('keterangan') }}</textarea>

        <div class="flex gap-3">
            <button type="submit" class="bg-green-600 hover:bg-green-500 text-white text-sm font-medium rounded-lg px-5 py-3">
                Ajukan perbaikan
            </button>
            <a href="{{ route('barang.scan') }}" class="border border-gray-600 text-white text-sm rounded-lg px-5 py-3 hover:bg-gray-800">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection