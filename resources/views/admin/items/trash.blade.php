@extends('layouts.app')

@section('content')
    <style>[x-cloak] { display: none !important; }</style>

    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="page-title">Trash Barang</h2>
            <p class="page-desc">
                Aset yang telah dihapus. Pulihkan untuk mengembalikannya ke Master Data Barang,
                atau hapus permanen (tidak bisa dibatalkan).
            </p>
        </div>

        <a href="{{ route('barang.index') }}" class="btn btn-outline">
            &larr; Kembali ke Master Data Barang
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-secondary-50 text-secondary-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{
            show: false,
            type: null,
            action: '',
            name: '',
            openModal(type, action, name) {
                this.type = type;
                this.action = action;
                this.name = name;
                this.show = true;
            },
            close() { this.show = false; }
        }"
        @keydown.escape.window="close()">

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-app">
                    <thead>
                        <tr>
                            <th>{{ __('Foto') }}</th>
                            <th>{{ __('Item ID') }}</th>
                            <th>{{ __('Nama Barang') }}</th>
                            <th>{{ __('Kategori') }}</th>
                            <th>{{ __('Lokasi') }}</th>
                            <th>{{ __('Nomor Aktiva Tetap') }}</th>
                            <th>{{ __('Nilai Perolehan') }}</th>
                            <th>{{ __('Dihapus Pada') }}</th>
                            <th class="text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    @if ($item->gambar_url)
                                        <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_barang }}" loading="lazy"
                                            class="h-12 w-12 rounded-lg border border-gray-200 object-cover">
                                    @else
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="cell-id">{{ $item->item_id }}</td>
                                <td class="font-medium text-gray-800">{{ $item->nama_barang }}</td>
                                <td>{{ $item->category->nama_kategori ?? '-' }}</td>
                                <td>{{ $item->location->nama_lokasi ?? '-' }}</td>
                                <td>{{ $item->nomor_aktiva_tetap ?? '-' }}</td>
                                <td class="font-medium text-gray-800">Rp {{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                                <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Pulihkan --}}
                                        <button type="button"
                                            class="rounded-lg bg-green-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-800"
                                            @click="openModal('restore', @js(route('barang.restore', $item->id)), @js($item->nama_barang))">
                                            Pulihkan
                                        </button>

                                        {{-- Hapus permanen --}}
                                        @if ($item->transactions_count > 0)
                                            <button type="button" disabled
                                                title="{{ __('Punya riwayat transaksi, tidak bisa dihapus permanen') }}"
                                                class="cursor-not-allowed rounded-lg bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-400">
                                                Hapus Permanen
                                            </button>
                                        @else
                                            <button type="button"
                                                class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                                @click="openModal('force-delete', @js(route('barang.force-delete', $item->id)), @js($item->nama_barang))">
                                                Hapus Permanen
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                    {{ __('Trash kosong.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-pad !pt-4">
                {{ $items->links() }}
            </div>
        </div>

        {{-- Modal konfirmasi (dipakai untuk Pulihkan & Hapus Permanen) --}}
        <div x-show="show" x-cloak x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            role="dialog" aria-modal="true">
            <div @click.outside="close()" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900"
                    x-text="type === 'restore' ? 'Pulihkan barang' : 'Hapus permanen'"></h3>

                <p class="mt-4 text-sm text-gray-600">
                    <span class="font-medium text-gray-800" x-text="name"></span>
                    <template x-if="type === 'restore'">
                        <span> — akan dikembalikan ke Master Data Barang.</span>
                    </template>
                    <template x-if="type === 'force-delete'">
                        <span> — data dan foto akan hilang dan tidak bisa dikembalikan.</span>
                    </template>
                </p>

                <form method="POST" :action="action" class="mt-6">
                    @csrf
                    <input type="hidden" name="_method" :value="type === 'restore' ? 'PATCH' : 'DELETE'">

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="close()" class="btn btn-outline">Batal</button>
                        <button type="submit"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-white"
                            :class="type === 'restore' ? 'bg-green-700 hover:bg-green-800' : 'bg-red-600 hover:bg-red-700'"
                            x-text="type === 'restore' ? 'Pulihkan' : 'Hapus permanen'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection