@extends('layouts.app')

@section('content')

    @php
        // Warna badge per status, memakai class badge-* yang sudah ada di CSS
        $badgeClass = [
            'menunggu_approval' => 'badge-amber',
            'diproses'          => 'badge-blue',
            'disetujui'         => 'badge-green',
            'dalam_perbaikan'   => 'badge-blue',
            'selesai'           => 'badge-green',
            'dikembalikan'      => 'badge-green',
            'ditolak'           => 'badge-red',
        ];
    @endphp

    <div class="mb-6">
        <h2 class="page-title">Riwayat Pengajuan Saya</h2>
        <p class="page-desc">Semua pengajuan peminjaman, perbaikan, dan laporan kerusakan Anda.</p>
    </div>

    {{-- Panel Filter --}}
    <div class="card card-pad mb-6">
        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">

                <div>
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-control">
                        <option value="">-- Semua --</option>
                        @foreach ($jenisLabels as $kode => $label)
                            <option value="{{ $kode }}" {{ request('jenis') === $kode ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">-- Semua --</option>
                        @foreach ($statusLabels as $kode => $label)
                            <option value="{{ $kode }}" {{ request('status') === $kode ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
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

            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                @if (request()->anyFilled(['jenis', 'status', 'date_from', 'date_to', 'sort']))
                    <a href="{{ url()->current() }}" class="text-sm text-gray-500 hover:underline">Reset filter</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Barang</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        <tr>
                            <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $jenisLabels[$trx->jenis_transaksi] ?? $trx->jenis_transaksi }}</td>
                            <td class="font-medium text-gray-800">
                                {{ $trx->item->nama_barang ?? '-' }}
                                <span class="block text-xs font-normal text-gray-500">{{ $trx->item->item_id ?? '' }}</span>
                            </td>
                            <td>{{ $trx->keterangan ?: '-' }}</td>
                            <td>
                                <span class="badge {{ $badgeClass[$trx->status] ?? 'badge-slate' }}">
                                    {{ $statusLabels[$trx->status] ?? str_replace('_', ' ', ucfirst($trx->status)) }}
                                </span>

                                @if ($trx->status === 'ditolak' && $trx->alasan_penolakan)
                                    <div class="mt-2 p-2 rounded-lg bg-error-50 text-error-700 text-xs max-w-xs">
                                        <span class="font-semibold">
                                            Ditolak{{ $trx->penolak ? ' oleh ' . ($trx->penolak->role === 'gm' ? 'General Manager' : 'Admin') : '' }}:
                                        </span>
                                        {{ $trx->alasan_penolakan }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500">
                                Belum ada riwayat transaksi.
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

@endsection