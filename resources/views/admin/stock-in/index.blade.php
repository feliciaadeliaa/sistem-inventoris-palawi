@extends('layouts.app')

@section('content')

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Stock In</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Konfirmasi pengembalian barang yang sedang dipinjam.</p>
        </div>

        <div class="flex gap-2">
            <button type="button" id="btn-download" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Download Excel
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
            {{ session('success') }}
        </div>
    @endif

{{-- Panel Filter --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm mb-6">
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

        <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-6 py-2.5 rounded-lg">
            Terapkan Filter
        </button>
    </form>
</div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">

        <label class="flex items-center gap-2 mb-4 text-sm text-gray-700 dark:text-gray-300">
            <input type="checkbox" id="select-all">
            Pilih Semua
        </label>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-brand-600 dark:text-brand-400 uppercase">Nama Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-brand-600 dark:text-brand-400 uppercase">Dipinjam Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-brand-600 dark:text-brand-400 uppercase">Tanggal Pinjam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-brand-600 dark:text-brand-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-brand-600 dark:text-brand-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3"><input type="checkbox" class="row-checkbox" value="{{ $trx->id }}"></td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $trx->item->nama_barang }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->user->name }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            @if ($trx->status === 'disetujui')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">Sedang Dipinjam</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Dikembalikan</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($trx->status === 'disetujui')
                                <form action="{{ route('admin.stock-in.confirm-return', $trx) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1.5 rounded text-sm hover:bg-green-700">
                                        Konfirmasi Kembali
                                    </button>
                                </form>
                            @else
                                <span class="bg-gray-400 text-white px-3 py-1.5 rounded text-sm cursor-not-allowed">
                                    Sudah Dikembalikan
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            Belum ada riwayat peminjaman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
    document.getElementById('select-all').addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('btn-download').addEventListener('click', function () {
        const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        const params = new URLSearchParams(window.location.search);
        if (selected.length > 0) {
            params.set('selected_ids', selected.join(','));
        }
        window.location.href = `{{ route('admin.stock-in.export') }}?${params.toString()}`;
    });
    </script>
    @endpush
@endsection