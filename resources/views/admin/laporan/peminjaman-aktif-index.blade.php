@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <h2 class="page-title">
            Laporan dan Ekspor
        </h2>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.laporan.peminjaman-aktif.export-pdf', request()->query()) }}" target="_blank"
                class="btn btn-outline">
                Ekspor PDF
            </a>
            <a href="{{ route('admin.laporan.peminjaman-aktif.export-excel', request()->query()) }}"
                class="btn btn-outline">
                Ekspor Excel
            </a>
        </div>
    </div>

    @include('admin.laporan._tabs', ['active' => 'peminjaman-aktif'])

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.laporan.peminjaman-aktif.index') }}" class="mb-4 card card-pad">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="form-label">Status</label>
                <select name="status_pinjam" class="form-control">
                    <option value="">-- Semua --</option>
                    <option value="aktif" {{ request('status_pinjam') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="terlambat" {{ request('status_pinjam') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div>
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-control">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">
                Terapkan
            </button>
            @if (request()->anyFilled(['status_pinjam', 'location_id', 'category_id']))
                <a href="{{ route('admin.laporan.peminjaman-aktif.index') }}" class="text-sm text-gray-500 hover:underline">
                    Reset filter
                </a>
            @endif
        </div>
    </form>

    {{-- Tabel --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Dipinjam Oleh</th>
                        <th>Tgl Disetujui</th>
                        <th>Estimasi Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        @php
                            $terlambat = $trx->tanggal_kembali_estimasi && $trx->tanggal_kembali_estimasi->isPast();
                        @endphp
                        <tr>
                            <td class="font-medium text-gray-800">
                                {{ $trx->item->nama_barang ?? '-' }}
                            </td>
                            <td>
                                {{ $trx->user->name ?? '-' }}
                            </td>
                            <td>
                                {{ $trx->approved_at?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>
                                {{ $trx->tanggal_kembali_estimasi?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>
                                <span class="badge {{ $terlambat ? 'badge-red' : 'badge-green' }}">
                                    {{ $terlambat ? 'Terlambat' : 'Aktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                Tidak ada barang yang sedang dipinjam.
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