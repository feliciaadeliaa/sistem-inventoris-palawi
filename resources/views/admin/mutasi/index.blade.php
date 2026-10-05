@extends('layouts.app')

@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h2 class="page-title">Mutasi Lokasi</h2>
        <p class="page-desc">Menunggu approval General Manager sebelum lokasi barang berubah</p>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 p-4 bg-success-50 text-success-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 p-4 bg-error-50 text-error-700 rounded-lg">
        {{ session('error') }}
    </div>
@endif


<div class="card card-pad mb-8">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
    <div>
        <label class="form-label">Kategori</label>
        <select id="filter-category" class="form-control">
            <option value="">-- Semua --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->category_id }}">{{ $category->nama_kategori }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Lokasi</label>
        <select id="filter-location" class="form-control">
            <option value="">-- Semua --</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}">{{ $location->nama_lokasi }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Kondisi</label>
        <select id="filter-kondisi" class="form-control">
            <option value="">-- Semua --</option>
            @foreach (\App\Models\Item::KONDISI_LABELS as $kode => $label)
                <option value="{{ $kode }}">{{ $kode }} - {{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Status</label>
        <select id="filter-status" class="form-control">
            <option value="">-- Semua --</option>
            @foreach (\App\Models\Item::STATUS_LABELS as $kode => $label)
                <option value="{{ $kode }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">AT/IBAT</label>
        <select id="filter-golongan" class="form-control">
            <option value="">-- Semua --</option>
            @foreach ($golonganOptions as $golongan)
                <option value="{{ $golongan }}">{{ $golongan }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Tahun Perolehan</label>
        <div class="flex items-center gap-1">
            <input type="number" id="filter-tahun-dari" placeholder="Dari" class="form-control">
            <span class="text-gray-400">-</span>
            <input type="number" id="filter-tahun-sampai" placeholder="Sampai" class="form-control">
        </div>
    </div>
</div>

    <input
        type="text"
        id="search-barang"
        placeholder="Cari nama atau kode barang"
        value="{{ $selectedItem->nama_barang ?? '' }}"
        class="form-control h-auto px-4 py-3 mb-2"
        autocomplete="off"
    >
    <div id="search-results" class="space-y-2 mb-4"></div>

    <div id="form-mutasi" class="{{ $selectedItem ? '' : 'hidden' }}">
        <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-500">Barang dipilih</p>
            <p id="info-nama" class="font-semibold text-gray-800">{{ $selectedItem->nama_barang ?? '' }}</p>
            <p id="info-kode" class="text-sm text-gray-500">
                {{ $selectedItem->item_id ?? '' }} &bull; Lokasi saat ini: <span id="info-lokasi">{{ $selectedItem->location->nama_lokasi ?? '' }}</span>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.transaksi.mutasi.store') }}">
            @csrf
            <input type="hidden" name="item_id" id="input-item-id" value="{{ $selectedItem->item_id ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end mb-4">
                <div>
                    <label class="form-label">Lokasi Asal</label>
                    <input type="text" id="lokasi-asal-display" readonly
                        value="{{ $selectedItem->location->nama_lokasi ?? '' }}"
                        class="form-control bg-gray-100 text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label for="lokasi_tujuan_id" class="form-label">Lokasi Tujuan</label>
                    <select name="lokasi_tujuan_id" id="lokasi_tujuan_id" required class="form-control">
                        <option value="">-- Pilih Lokasi Baru --</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="form-label">Keterangan (opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="2" class="form-control h-auto py-2"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Ajukan Mutasi
            </button>
        </form>
    </div>
</div>

<div class="card card-pad mb-6">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <h3 class="panel-title mb-0">Riwayat Mutasi Terbaru</h3>
        <button type="button" id="btn-download-mutasi" class="btn btn-outline">
            Download Excel
        </button>
    </div>

    <form method="GET">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4 items-end">
            <div>
                <label class="form-label">Tanggal Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Urutkan</label>
                <select name="sort" class="form-control">
                    <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary w-full md:w-auto">
                    Terapkan Filter
                </button>
                @if (request()->anyFilled(['date_from', 'date_to', 'sort']))
                    <a href="{{ url()->current() }}" class="text-sm text-gray-500 hover:underline whitespace-nowrap">
                        Reset filter
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">

        <div class="mb-4 flex items-center justify-between px-4 pt-4">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" id="select-all-mutasi" class="h-4 w-4 rounded border-gray-300 accent-brand-600">
                Pilih Semua (halaman ini)
            </label>

            <div class="flex items-center gap-3 text-sm text-gray-600">
                <span><span id="selected-count-mutasi" class="font-semibold text-brand-700">0</span> item terpilih</span>
                <button type="button" id="clear-selection-btn-mutasi" class="text-red-500 hover:underline text-xs">
                    Reset Pilihan
                </button>
            </div>
        </div>

        <table class="table-app">
            <thead>
                <tr>
                    <th></th>
                    <th>Nama Barang</th>
                    <th>Lokasi Asal &rarr; Tujuan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $trx)
                    <tr>
                        <td><input type="checkbox" class="row-checkbox-mutasi h-4 w-4 rounded border-gray-300 accent-brand-600" value="{{ $trx->id }}"></td>
                        <td class="font-medium text-gray-800">{{ $trx->item->nama_barang }}</td>
                        <td>
                            {{ $trx->lokasiAsal->nama_lokasi ?? '-' }} &rarr; {{ $trx->lokasiTujuan->nama_lokasi ?? '-' }}
                        </td>
                        <td>
                            <span @class([
                                'badge',
                                'badge-amber' => $trx->status === 'menunggu_approval',
                                'badge-green' => in_array($trx->status, ['disetujui', 'selesai']),
                                'badge-red' => $trx->status === 'ditolak',
                            ])>
                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                            </span>
                        </td>
                        <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500">Belum ada riwayat mutasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-pad !pt-4">
        {{ $riwayat->links() }}
    </div>
</div>

@push('scripts')
<script>
const MUTASI_SELECTION_KEY = 'mutasi_selected_ids';

function getSelectedIdsMutasi() {
    try {
        return new Set(JSON.parse(sessionStorage.getItem(MUTASI_SELECTION_KEY)) || []);
    } catch (e) {
        return new Set();
    }
}

function saveSelectedIdsMutasi(idsSet) {
    sessionStorage.setItem(MUTASI_SELECTION_KEY, JSON.stringify(Array.from(idsSet)));
}

function updateCounterMutasi() {
    document.getElementById('selected-count-mutasi').textContent = getSelectedIdsMutasi().size;
}

function updateSelectAllStateMutasi() {
    const checkboxes = document.querySelectorAll('.row-checkbox-mutasi');
    const selectAll = document.getElementById('select-all-mutasi');
    if (!checkboxes.length) {
        selectAll.checked = false;
        return;
    }
    selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
}

function syncCheckboxesWithStorageMutasi() {
    const selected = getSelectedIdsMutasi();
    document.querySelectorAll('.row-checkbox-mutasi').forEach(cb => {
        cb.checked = selected.has(cb.value);
    });
    updateSelectAllStateMutasi();
    updateCounterMutasi();
}

document.addEventListener('DOMContentLoaded', function () {
    const searchUrl = "{{ route('barang.cari') }}";
    const searchInput = document.getElementById('search-barang');
    const searchResults = document.getElementById('search-results');
    const formMutasi = document.getElementById('form-mutasi');
    let debounceTimer;

    const filterIds = [
        'filter-category', 'filter-location', 'filter-kondisi',
        'filter-status', 'filter-golongan', 'filter-tahun-dari', 'filter-tahun-sampai'
    ];

    function pilihBarang(item) {
        document.getElementById('input-item-id').value = item.item_id;
        document.getElementById('info-nama').textContent = item.nama_barang;
        document.getElementById('info-kode').innerHTML = `${item.item_id} &bull; Lokasi saat ini: <span id="info-lokasi">${item.lokasi}</span>`;
        document.getElementById('lokasi-asal-display').value = item.lokasi;
        formMutasi.classList.remove('hidden');
        searchResults.innerHTML = '';
        searchInput.value = item.nama_barang;
    }

    function buildParams() {
        const params = new URLSearchParams();
        const q = searchInput.value.trim();
        if (q) params.set('q', q);

        params.set('category_id', document.getElementById('filter-category').value);
        params.set('location_id', document.getElementById('filter-location').value);
        params.set('kondisi', document.getElementById('filter-kondisi').value);
        params.set('status', document.getElementById('filter-status').value);
        params.set('at_ibat', document.getElementById('filter-golongan').value);
        params.set('tahun_dari', document.getElementById('filter-tahun-dari').value);
        params.set('tahun_sampai', document.getElementById('filter-tahun-sampai').value);

        return params;
    }

    function renderResults(items) {
        searchResults.innerHTML = '';
        items.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'w-full text-left border border-gray-300 bg-white text-gray-800 rounded-lg px-4 py-3 hover:bg-gray-50';
            btn.innerHTML = `<span class="font-medium">${item.nama_barang}</span>
                              <span class="block text-gray-500 text-sm">${item.item_id} &bull; ${item.lokasi}</span>`;
            btn.addEventListener('click', () => pilihBarang(item));
            searchResults.appendChild(btn);
        });
    }

    function runSearch() {
        const params = buildParams();
        const q = params.get('q') || '';
        const hasFilter = ['category_id', 'location_id', 'kondisi', 'status', 'at_ibat', 'tahun_dari', 'tahun_sampai']
            .some(key => params.get(key));

        if (q.length < 2 && !hasFilter) {
            searchResults.innerHTML = '';
            return;
        }

        fetch(`${searchUrl}?${params.toString()}`)
            .then(res => res.json())
            .then(renderResults);
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(runSearch, 300);
    });

    filterIds.forEach(id => {
        document.getElementById(id).addEventListener('change', runSearch);
    });

    // --- Checkbox & Download Riwayat Mutasi ---
    syncCheckboxesWithStorageMutasi();

    document.querySelectorAll('.row-checkbox-mutasi').forEach(cb => {
        cb.addEventListener('change', () => {
            const selected = getSelectedIdsMutasi();
            cb.checked ? selected.add(cb.value) : selected.delete(cb.value);
            saveSelectedIdsMutasi(selected);
            updateSelectAllStateMutasi();
            updateCounterMutasi();
        });
    });

    document.getElementById('select-all-mutasi').addEventListener('change', function () {
        const selected = getSelectedIdsMutasi();
        document.querySelectorAll('.row-checkbox-mutasi').forEach(cb => {
            cb.checked = this.checked;
            this.checked ? selected.add(cb.value) : selected.delete(cb.value);
        });
        saveSelectedIdsMutasi(selected);
        updateCounterMutasi();
    });

    document.getElementById('clear-selection-btn-mutasi').addEventListener('click', () => {
        sessionStorage.removeItem(MUTASI_SELECTION_KEY);
        syncCheckboxesWithStorageMutasi();
    });

    document.getElementById('btn-download-mutasi').addEventListener('click', function () {
        const selected = Array.from(getSelectedIdsMutasi());
        const params = new URLSearchParams(window.location.search);
        if (selected.length > 0) {
            params.set('selected_ids', selected.join(','));
        }
        window.location.href = `{{ route('admin.transaksi.mutasi.export') }}?${params.toString()}`;
    });
});
</script>
@endpush
@endsection