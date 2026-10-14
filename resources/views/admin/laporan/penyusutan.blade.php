@extends('layouts.app')

@section('content')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $periodeLabel = $mode === 'tahunan'
        ? 'Tahun ' . $tahun
        : $namaBulan[$bulan] . ' ' . $tahun;
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <h2 class="page-title">Laporan Penyusutan Aset</h2>
        <p class="page-desc">
            Periode: {{ $periodeLabel }}
            ({{ $awal->format('d/m/Y') }} – {{ $akhir->format('d/m/Y') }})
        </p>

        <div class="mt-3">
            @if ($status === 'berjalan')
                <span class="badge badge-amber">
                    Berjalan – dihitung sampai {{ $hariIni->format('d/m/Y') }}
                </span>
            @elseif ($status === 'final')
                <span class="badge badge-green">
                    Final – berakhir pada {{ $akhir->format('d/m/Y') }}
                </span>
            @else
                <span class="badge badge-slate">
                    Periode belum dimulai
                </span>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <button type="submit" form="export-form" class="btn btn-outline"
            title="{{ __('Unduh baris yang dicentang. Tanpa pilihan, semua baris diunduh.') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 15V3" />
                <path d="m7 10 5 5 5-5" />
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            </svg>
            Download Excel
        </button>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.laporan.penyusutan') }}"
      x-data="{ mode: '{{ $mode }}' }"
      class="card card-pad mb-4">

    {{-- Bawa kata kunci dari search bar header saat ganti periode --}}
    <input type="hidden" name="q" value="{{ $search }}">

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div>
            <label for="mode" class="form-label">Mode</label>
            <select id="mode" name="mode" x-model="mode" class="form-control">
                <option value="bulanan">Bulanan</option>
                <option value="tahunan">Tahunan</option>
            </select>
        </div>

        <div x-show="mode === 'bulanan'">
            <label for="bulan" class="form-label">Bulan</label>
            <select id="bulan" name="bulan" class="form-control">
                @foreach ($namaBulan as $no => $nama)
                    <option value="{{ $no }}" @selected($no === $bulan)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" id="tahun" name="tahun" value="{{ $tahun }}" min="1990" max="2100"
                   class="form-control">
        </div>
    </div>

    <div class="flex items-center gap-3 mt-4">
        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
    </div>
</form>

{{-- Form export: membungkus tabel karena checkbox ada di dalamnya --}}
<form id="export-form" method="GET" action="{{ route('admin.laporan.penyusutan.export') }}">
    <input type="hidden" name="mode" value="{{ $mode }}">
    <input type="hidden" name="bulan" value="{{ $bulan }}">
    <input type="hidden" name="tahun" value="{{ $tahun }}">
    <input type="hidden" name="q" value="{{ $search }}">

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">

            <div class="mb-4 flex items-center justify-between px-4 pt-4">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" id="select-all-checkbox"
                        class="h-4 w-4 rounded border-gray-300 accent-brand-600">
                    {{ __('Pilih Semua (halaman ini)') }}
                </label>

                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <span id="export-hint" class="text-xs text-gray-400">
                        {{ __('Tanpa pilihan, semua baris diunduh.') }}
                    </span>
                    <span><span id="selected-count" class="font-semibold text-brand-700">0</span> {{ __('item terpilih') }}</span>
                    <button type="button" id="clear-selection-btn" class="text-red-500 hover:underline text-xs">
                        {{ __('Reset Pilihan') }}
                    </button>
                </div>
            </div>

            <table class="table-app">
                <thead>
                    <tr>
                        <th class="px-4 py-3">
                            <span class="sr-only">{{ __('Pilih') }}</span>
                        </th>
                        <th>No</th>
                        <th>ID Item</th>
                        <th>Nama Barang</th>
                        <th>Jenis Aktiva</th>
                        <th>No. Aktiva Tetap</th>
                        <th>Tgl Terima</th>
                        <th class="text-right">Masa Manfaat</th>
                        <th class="text-right">Nilai Perolehan</th>
                        <th class="text-right">Penyusutan Periode</th>
                        <th class="text-right">Akumulasi</th>
                        <th class="text-right">Nilai Buku</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php $item = $row['item']; @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                    class="h-4 w-4 rounded border-gray-300 accent-brand-600 row-checkbox">
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td class="cell-id">{{ $item->item_id }}</td>
                            <td class="font-medium text-gray-800">{{ $item->nama_barang }}</td>
                            <td>{{ $item->category->jenis_aktiva_tetap ?? '-' }}</td>
                            <td>{{ $item->nomor_aktiva_tetap ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') }}</td>
                            <td class="text-right">{{ $item->masa_manfaat }} th</td>
                            <td class="text-right">{{ $rp($item->nilai_perolehan) }}</td>
                            <td class="text-right">{{ $rp($row['penyusutan_periode']) }}</td>
                            <td class="text-right">{{ $rp($row['akumulasi']) }}</td>
                            <td class="text-right font-medium text-gray-800">{{ $rp($row['nilai_buku']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="!py-8 text-center text-gray-500">
                                {{ $search !== '' ? "Tidak ada aset yang cocok dengan \"{$search}\"." : 'Tidak ada aset pada periode ini.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
    const PENYUSUTAN_SELECTION_KEY = 'penyusutan_selected_ids';

    function getPenyusutanSelectedIds() {
        try {
            return new Set(JSON.parse(sessionStorage.getItem(PENYUSUTAN_SELECTION_KEY)) || []);
        } catch (e) {
            return new Set();
        }
    }

    function savePenyusutanSelectedIds(idsSet) {
        sessionStorage.setItem(PENYUSUTAN_SELECTION_KEY, JSON.stringify(Array.from(idsSet)));
    }

    function updatePenyusutanCounter() {
        const count = getPenyusutanSelectedIds().size;
        const countEl = document.getElementById('selected-count');
        const hintEl = document.getElementById('export-hint');
        if (countEl) countEl.textContent = count;
        if (hintEl) hintEl.classList.toggle('hidden', count > 0);
    }

    function updatePenyusutanSelectAll() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const selectAll = document.getElementById('select-all-checkbox');
        if (!selectAll) return;
        selectAll.checked = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
    }

    function syncPenyusutanCheckboxes() {
        const selected = getPenyusutanSelectedIds();
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = selected.has(cb.value);
        });
        updatePenyusutanSelectAll();
        updatePenyusutanCounter();
    }

    // Sinkron saat halaman dimuat, dan setelah search bar header mengganti isi halaman
    document.addEventListener('DOMContentLoaded', syncPenyusutanCheckboxes);
    document.addEventListener('main-content:updated', syncPenyusutanCheckboxes);

    // Event delegation: tetap jalan walau isi tabel diganti oleh search bar header
    document.addEventListener('change', function (e) {
        if (e.target.matches('.row-checkbox')) {
            const selected = getPenyusutanSelectedIds();
            e.target.checked ? selected.add(e.target.value) : selected.delete(e.target.value);
            savePenyusutanSelectedIds(selected);
            updatePenyusutanSelectAll();
            updatePenyusutanCounter();
            return;
        }

        if (e.target.id === 'select-all-checkbox') {
            const selected = getPenyusutanSelectedIds();
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = e.target.checked;
                e.target.checked ? selected.add(cb.value) : selected.delete(cb.value);
            });
            savePenyusutanSelectedIds(selected);
            updatePenyusutanCounter();
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('#clear-selection-btn')) {
            sessionStorage.removeItem(PENYUSUTAN_SELECTION_KEY);
            syncPenyusutanCheckboxes();
        }
    });

    // Sebelum download, sisipkan pilihan yang tidak sedang tertampil (misalnya tersaring pencarian)
    document.addEventListener('submit', function (e) {
        if (e.target.id !== 'export-form') return;

        const form = e.target;
        const visibleIds = new Set(
            Array.from(document.querySelectorAll('.row-checkbox')).map(cb => cb.value)
        );
        form.querySelectorAll('input[data-extra-id]').forEach(el => el.remove());

        getPenyusutanSelectedIds().forEach(id => {
            if (!visibleIds.has(id)) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'ids[]';
                hidden.value = id;
                hidden.dataset.extraId = '1';
                form.appendChild(hidden);
            }
        });
    });
</script>
@endsection