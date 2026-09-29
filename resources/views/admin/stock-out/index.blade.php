@extends('layouts.app')

@section('content')

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="page-title">Stock Out</h2>
            <p class="page-desc">Antrian dan riwayat pengajuan peminjaman barang.</p>
        </div>

        <button type="button" id="btn-download" class="btn btn-outline">
            Download Excel
        </button>
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

    {{-- Panel Filter --}}
    <div class="card card-pad mb-6">
        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">

                <div class="md:col-span-2">
                    <label class="form-label">Tanggal Transaksi</label>
                    <div class="flex items-center gap-1">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control w-full">
                        <span class="text-gray-400">-</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control w-full">
                    </div>
                </div>

                <div>
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-control">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                Terapkan Filter
            </button>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">

            <div class="mb-4 flex items-center justify-between px-4 pt-4">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 accent-brand-600">
                    Pilih Semua (halaman ini)
                </label>

                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <span><span id="selected-count" class="font-semibold text-brand-700">0</span> item terpilih</span>
                    <button type="button" id="clear-selection-btn" class="text-red-500 hover:underline text-xs">
                        Reset Pilihan
                    </button>
                </div>
            </div>

            <table class="table-app">
                <thead>
                    <tr>
                        <th></th>
                        <th>Barang</th>
                        <th>Pengaju</th>
                        <th>Keterangan</th>
                        <th>Estimasi Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        <tr>
                            <td><input type="checkbox" class="row-checkbox h-4 w-4 rounded border-gray-300 accent-brand-600" value="{{ $trx->id }}"></td>
                            <td class="font-medium text-gray-800">{{ $trx->item->nama_barang }}</td>
                            <td>{{ $trx->user->name }}</td>
                            <td>{{ $trx->keterangan }}</td>
                            <td>{{ $trx->tanggal_kembali_estimasi?->format('d M Y') }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-amber' => $trx->status === 'menunggu_approval',
                                    'badge-blue' => $trx->status === 'disetujui',
                                    'badge-red' => $trx->status === 'ditolak',
                                    'badge-green' => $trx->status === 'dikembalikan',
                                ])>
                                    {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                                </span>
                            </td>
                            <td class="space-x-1">
                                @if ($trx->status === 'menunggu_approval')
                                    <form action="{{ route('admin.transaksi.stock-out.approve', $trx) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm text-white bg-secondary-600 hover:bg-secondary-700">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.transaksi.stock-out.reject', $trx) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm text-white bg-error-600 hover:bg-error-700">Tolak</button>
                                    </form>
                                @elseif ($trx->status === 'disetujui')
                                    <span class="text-xs text-gray-500">Menunggu pengembalian</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500">
                                Belum ada pengajuan peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-pad !pt-4">
            {{ $transactions->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
const STOCKIN_SELECTION_KEY = 'stockout_selected_ids';

function getSelectedIds() {
    try {
        return new Set(JSON.parse(sessionStorage.getItem(STOCKIN_SELECTION_KEY)) || []);
    } catch (e) {
        return new Set();
    }
}

function saveSelectedIds(idsSet) {
    sessionStorage.setItem(STOCKIN_SELECTION_KEY, JSON.stringify(Array.from(idsSet)));
}

function updateCounter() {
    document.getElementById('selected-count').textContent = getSelectedIds().size;
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectAll = document.getElementById('select-all');
    if (!checkboxes.length) {
        selectAll.checked = false;
        return;
    }
    selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
}

function syncCheckboxesWithStorage() {
    const selected = getSelectedIds();
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = selected.has(cb.value);
    });
    updateSelectAllState();
    updateCounter();
}

document.addEventListener('DOMContentLoaded', () => {
    syncCheckboxesWithStorage();

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const selected = getSelectedIds();
            cb.checked ? selected.add(cb.value) : selected.delete(cb.value);
            saveSelectedIds(selected);
            updateSelectAllState();
            updateCounter();
        });
    });

    document.getElementById('select-all').addEventListener('change', function () {
        const selected = getSelectedIds();
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = this.checked;
            this.checked ? selected.add(cb.value) : selected.delete(cb.value);
        });
        saveSelectedIds(selected);
        updateCounter();
    });

    document.getElementById('clear-selection-btn').addEventListener('click', () => {
        sessionStorage.removeItem(STOCKIN_SELECTION_KEY);
        syncCheckboxesWithStorage();
    });

    document.getElementById('btn-download').addEventListener('click', function () {
        const selected = Array.from(getSelectedIds());
        const params = new URLSearchParams(window.location.search);
        if (selected.length > 0) {
            params.set('selected_ids', selected.join(','));
        }
        window.location.href = `{{ route('admin.stock-in.export') }}?${params.toString()}`;
    });
});
    </script>
    @endpush
@endsection