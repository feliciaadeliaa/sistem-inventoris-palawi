@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Master Data Barang') }}
        </h2>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex justify-end">
        <a href="{{ route('barang.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + {{ __('Tambah Barang') }}
        </a>
    </div>

    {{-- Form cetak label: SATU form membungkus seluruh tabel, checkbox per baris di dalamnya --}}
    <form action="{{ route('barang.print-labels') }}" method="GET" target="_blank">

        <div class="mb-3 flex items-center justify-between">
            <label class="text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" onclick="document.querySelectorAll('.qr-checkbox').forEach(cb => cb.checked = this.checked)">
                {{ __('Pilih Semua') }}
            </label>

            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                {{ __('Cetak Label Terpilih') }}
            </button>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">
                            <span class="sr-only">{{ __('Pilih') }}</span>
                        </th>
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
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="qr-checkbox">
                            </td>
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
                                <button
                                    type="button"
                                    onclick="openQrModal('{{ route('barang.qr', $item) }}', '{{ $item->nama_barang }}')"
                                    class="text-green-600 hover:underline"
                                >
                                    {{ __('Lihat QR') }}
                                </button>
                                <a href="{{ route('barang.qr.download', $item) }}" class="text-green-600 hover:underline">{{ __('Download') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                {{ __('Belum ada data barang.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-4">
        {{ $items->links() }}
    </div>

    {{-- Modal QR --}}
    <div id="qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg p-6 w-80 text-center relative">
            <button
                onclick="closeQrModal()"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 text-xl"
            >&times;</button>

            <h3 id="qr-modal-title" class="font-semibold mb-4 text-gray-800"></h3>

            <img id="qr-modal-image" src="" alt="QR Code" class="mx-auto w-56 h-56">

            
               <a id="qr-modal-download"
                href="#"
                class="inline-block mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
            >
                {{ __('Download') }}
            </a>
        </div>
    </div>

    <script>
        function openQrModal(qrUrl, itemName) {
            document.getElementById('qr-modal-title').innerText = itemName;
            document.getElementById('qr-modal-image').src = qrUrl;
            document.getElementById('qr-modal-download').href = qrUrl.replace('/qr', '/qr/download');
            document.getElementById('qr-modal').classList.remove('hidden');
            document.getElementById('qr-modal').classList.add('flex');
        }

        function closeQrModal() {
            document.getElementById('qr-modal').classList.add('hidden');
            document.getElementById('qr-modal').classList.remove('flex');
        }
    </script>
@endsection