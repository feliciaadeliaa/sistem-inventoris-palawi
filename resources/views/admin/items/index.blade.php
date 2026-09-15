@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
                Master Data Barang
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola data aset dan barang inventaris.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden">
                <button type="submit" form="print-labels-form" formaction="{{ route('barang.print-labels') }}" formtarget="_blank"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-r border-gray-300 dark:border-gray-700">
                    Cetak PDF
                </button>
                <button type="submit" form="print-labels-form" formaction="{{ route('barang.print-labels-png') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Cetak PNG (ZIP)
                </button>
            </div>

            <a href="{{ route('barang.create') }}" class="bg-brand-500 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-brand-600">
                + Tambah Barang
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form cetak label: membungkus filter + tabel, karena checkbox ada di dalam tabel --}}
    <form id="print-labels-form" action="{{ route('barang.print-labels') }}" method="GET" target="_blank">

        {{-- Filter kategori (kiri) + Pilih Semua (kanan) --}}
        <div class="mb-4 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <select name="category_id" onchange="this.form.submit()"
                    class="dark:bg-dark-900 h-11 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                    <option value="">{{ __('-- Semua Kategori --') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                @if (request()->filled('category_id'))
                    <a href="{{ route('barang.index') }}" class="text-sm text-gray-500 hover:underline dark:text-gray-400">
                        {{ __('Reset filter') }}
                    </a>
                @endif
            </div>

            <label class="text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" onclick="document.querySelectorAll('.qr-checkbox').forEach(cb => cb.checked = this.checked)">
                {{ __('Pilih Semua') }}
            </label>
        </div>

        {{-- Card putih HANYA membungkus tabel --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="sticky top-0 z-10 bg-white dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            <span class="sr-only">{{ __('Pilih') }}</span>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Item ID') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Nama Barang') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Kategori') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Lokasi') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Golongan AT') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Nomor Aktiva Tetap') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Tahun Perolehan') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Masa Manfaat') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Nilai Perolehan') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Tanggal Terima') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Kondisi') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="qr-checkbox">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->item_id }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->nama_barang }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->category->nama_kategori }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->location->nama_lokasi }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->golongan_at }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">-</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->tahun_perolehan }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->masa_manfaat }} {{ __('tahun') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">Rp {{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->tanggal_terima->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $item->kondisi === 'baik'
                                        ? 'bg-green-100 text-green-800'
                                        : ($item->kondisi === 'rusak_ringan' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ str_replace('_', ' ', $item->kondisi) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $item->status === 'tersedia'
                                        ? 'bg-green-100 text-green-800'
                                        : ($item->status === 'dipinjam' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ str_replace('_', ' ', $item->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('barang.edit', $item) }}"
                                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                        {{ __('Edit') }}
                                    </a>
                                    <button
                                        type="button"
                                        onclick="openQrModal('{{ route('barang.qr', $item) }}', '{{ $item->nama_barang }}')"
                                        class="px-3 py-1 bg-brand-500 text-white rounded hover:bg-brand-600"
                                    >
                                        {{ __('Lihat QR') }}
                                    </button>
                                    <a href="{{ route('barang.qr.download', ['item' => $item, 'format' => 'png']) }}"
                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                        PNG
                                    </a>
                                    <a href="{{ route('barang.qr.download', ['item' => $item, 'format' => 'pdf']) }}"
                                        class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                {{ __('Belum ada data barang.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $items->links() }}
            </div>
        </div>

    </form>

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