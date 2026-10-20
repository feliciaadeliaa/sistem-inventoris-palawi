@extends('layouts.app')

@section('content')

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="page-title">Riwayat Peminjaman Saya</h2>
            <p class="page-desc">Lihat riwayat pengajuan peminjaman barang Anda.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                + Ajukan Peminjaman Baru
            </a>
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
            <table class="table-app">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Keterangan</th>
                        <th>Estimasi Kembali</th>
                        <th>Status</th>
                        <th>Tanggal Kembali Aktual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        <tr>
                            <td class="font-medium text-gray-800">{{ $trx->item->nama_barang }}</td>
                            <td>{{ $trx->keterangan ?: '-' }}</td>
                            <td>{{ $trx->tanggal_kembali_estimasi?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-amber' => $trx->status === 'menunggu_approval',
                                    'badge-blue' => $trx->status === 'diproses',
                                    'badge-green' => in_array($trx->status, ['disetujui', 'dikembalikan']),
                                    'badge-red' => $trx->status === 'ditolak',
                                ])>
                                    @if ($trx->status === 'menunggu_approval')
                                        Menunggu admin
                                    @elseif ($trx->status === 'diproses')
                                        Menunggu persetujuan GM
                                    @else
                                        {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                                    @endif
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
                            <td>{{ $trx->tanggal_kembali_aktual?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500">
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

@endsection