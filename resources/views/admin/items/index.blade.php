@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-brand-900">
                Master Data Barang
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Kelola data aset dan barang inventaris.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="submit" form="print-labels-form" formaction="{{ route('barang.print-labels') }}" formtarget="_blank"
                class="btn btn-outline">
                Cetak as PDF
            </button>
            <button type="submit" form="print-labels-form" formaction="{{ route('barang.print-labels-png') }}"
                class="btn btn-outline">
                Cetak as PNG (ZIP)
            </button>
            <a href="{{ route('barang.create') }}" class="btn btn-primary">
                + Tambah Barang
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-secondary-50 text-secondary-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('barang.index') }}" class="mb-4 card card-pad">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Kondisi</label>
                <select name="kondisi" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach (\App\Models\Item::KONDISI_LABELS as $kode => $label)
                        <option value="{{ $kode }}" {{ request('kondisi') == $kode ? 'selected' : '' }}>
                            {{ $kode }} - {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach (\App\Models\Item::STATUS_LABELS as $kode => $label)
                        <option value="{{ $kode }}" {{ request('status') == $kode ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Golongan AT</label>
                <select name="golongan_at" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach ($golonganOptions as $golongan)
                        <option value="{{ $golongan }}" {{ request('golongan_at') == $golongan ? 'selected' : '' }}>
                            {{ $golongan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Tahun Perolehan</label>
                <div class="flex items-center gap-1">
                    <input type="number" name="tahun_dari" placeholder="Dari" value="{{ request('tahun_dari') }}"
                        class="form-control">
                    <span class="text-gray-400">-</span>
                    <input type="number" name="tahun_sampai" placeholder="Sampai" value="{{ request('tahun_sampai') }}"
                        class="form-control">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">
                Terapkan Filter
            </button>
            @if (request()->anyFilled(['category_id', 'location_id', 'kondisi', 'status', 'golongan_at', 'tahun_dari', 'tahun_sampai']))
                <a href="{{ route('barang.index') }}" class="text-sm text-gray-500 hover:underline">
                    Reset filter
                </a>
            @endif
        </div>
    </form>

    {{-- Form cetak label: membungkus tabel, karena checkbox ada di dalamnya --}}
    <form id="print-labels-form" method="GET">

        <div class="mb-4 flex items-center justify-start pl-4">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" class="h-4 w-4 rounded border-gray-300 accent-brand-600"
                    onclick="document.querySelectorAll('.qr-checkbox').forEach(cb => cb.checked = this.checked)">
                {{ __('Pilih Semua') }}
            </label>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-app">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">
                                <span class="sr-only">{{ __('Pilih') }}</span>
                            </th>
                            <th>{{ __('Item ID') }}</th>
                            <th>{{ __('Nama Barang') }}</th>
                            <th>{{ __('Kategori') }}</th>
                            <th>{{ __('Lokasi') }}</th>
                            <th>{{ __('Golongan AT') }}</th>
                            <th>{{ __('Nomor Aktiva Tetap') }}</th>
                            <th>{{ __('Tahun Perolehan') }}</th>
                            <th>{{ __('Masa Manfaat') }}</th>
                            <th>{{ __('Nilai Perolehan') }}</th>
                            <th>{{ __('Tanggal Terima') }}</th>
                            <th>{{ __('Kondisi') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="sticky-col text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                        class="h-4 w-4 rounded border-gray-300 accent-brand-600 qr-checkbox">
                                </td>
                                <td class="cell-id">{{ $item->item_id }}</td>
                                <td class="font-medium text-gray-800">{{ $item->nama_barang }}</td>
                                <td>{{ $item->category->nama_kategori }}</td>
                                <td>{{ $item->location->nama_lokasi }}</td>
                                <td>
                                    <span class="badge badge-round
                                        {{ $item->golongan_at === 'I' ? 'badge-teal'
                                            : ($item->golongan_at === 'II' ? 'badge-orange'
                                                : ($item->golongan_at === 'III' ? 'badge-amber' : 'badge-slate')) }}">
                                        {{ $item->golongan_at }}
                                    </span>
                                </td>
                                <td>{{ $item->nomor_aktiva_tetap ?? '-' }}</td>
                                <td>{{ $item->tahun_perolehan }}</td>
                                <td>{{ $item->masa_manfaat }} {{ __('tahun') }}</td>
                                <td class="font-medium text-gray-800">Rp {{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                                <td>{{ $item->tanggal_terima->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge
                                        {{ $item->kondisi === 'B'
                                            ? 'badge-green'
                                            : ($item->kondisi === 'BPR' ? 'badge-amber' : 'badge-red') }}">
                                        {{ $item->kondisi }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge
                                        {{ $item->status === 'tersedia'
                                            ? 'badge-green'
                                            : ($item->status === 'dipinjam' ? 'badge-blue' : 'badge-slate') }}">
                                        {{ str_replace('_', ' ', $item->status) }}
                                    </span>
                                </td>
                                <td class="sticky-col">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('barang.edit', $item) }}" class="btn btn-sm btn-primary">
                                            {{ __('Edit') }}
                                        </a>
                                        <button
                                            type="button"
                                            onclick="openQrModal('{{ route('barang.qr', $item) }}', '{{ $item->nama_barang }}')"
                                            class="btn btn-sm btn-outline"
                                        >
                                            {{ __('Lihat QR') }}
                                        </button>
                                        <a href="{{ route('barang.qr.download', $item) }}" class="btn btn-sm btn-ghost">
                                            {{ __('Download') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="px-4 py-6 text-center text-gray-500">
                                    {{ __('Belum ada data barang.') }}
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
                class="inline-block mt-4 btn btn-primary"
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