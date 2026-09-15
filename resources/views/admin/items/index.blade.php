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

        <div class="flex items-center gap-2">
            <button type="submit" form="print-labels-form" formaction="{{ route('barang.print-labels') }}" formtarget="_blank"
                class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-4 py-2 rounded">
                Cetak as PDF
            </button>
            <button type="submit" form="print-labels-form" formaction="{{ route('barang.print-labels-png') }}"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded">
                Cetak as PNG (ZIP)
            </button>
            <a href="{{ route('barang.create') }}" class="bg-brand-500 text-white px-4 py-2 rounded hover:bg-brand-600">
                + Tambah Barang
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('barang.index') }}" class="mb-4 bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kategori</label>
                <select name="category_id" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lokasi</label>
                <select name="location_id" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kondisi</label>
                <select name="kondisi" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach (\App\Models\Item::KONDISI_LABELS as $kode => $label)
                        <option value="{{ $kode }}" {{ request('kondisi') == $kode ? 'selected' : '' }}>
                            {{ $kode }} - {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach (\App\Models\Item::STATUS_LABELS as $kode => $label)
                        <option value="{{ $kode }}" {{ request('status') == $kode ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Golongan AT</label>
                <select name="golongan_at" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach ($golonganOptions as $golongan)
                        <option value="{{ $golongan }}" {{ request('golongan_at') == $golongan ? 'selected' : '' }}>
                            {{ $golongan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tahun Perolehan</label>
                <div class="flex items-center gap-1">
                    <input type="number" name="tahun_dari" placeholder="Dari" value="{{ request('tahun_dari') }}"
                        class="w-full h-10 rounded-lg border border-gray-300 bg-white px-2 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <span class="text-gray-400">-</span>
                    <input type="number" name="tahun_sampai" placeholder="Sampai" value="{{ request('tahun_sampai') }}"
                        class="w-full h-10 rounded-lg border border-gray-300 bg-white px-2 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded">
                Terapkan Filter
            </button>
            @if (request()->anyFilled(['category_id', 'location_id', 'kondisi', 'status', 'golongan_at', 'tahun_dari', 'tahun_sampai']))
                <a href="{{ route('barang.index') }}" class="text-sm text-gray-500 hover:underline dark:text-gray-400">
                    Reset filter
                </a>
            @endif
        </div>
    </form>

    {{-- Form cetak label: membungkus tabel, karena checkbox ada di dalamnya --}}
    <form id="print-labels-form" method="GET">

        <div class="mb-4 flex items-center justify-end">
            <label class="text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" onclick="document.querySelectorAll('.qr-checkbox').forEach(cb => cb.checked = this.checked)">
                {{ __('Pilih Semua') }}
            </label>
        </div>

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
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->nomor_aktiva_tetap ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->tahun_perolehan }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->masa_manfaat }} {{ __('tahun') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">Rp {{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $item->tanggal_terima->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $item->kondisi === 'B'
                                        ? 'bg-green-100 text-green-800'
                                        : ($item->kondisi === 'BPR' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $item->kondisi }}
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
                                    <a href="{{ route('barang.qr.download', $item) }}"
                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                        {{ __('Download') }}
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