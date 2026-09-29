@extends('layouts.app')

@section('content')
  <div class="flex flex-wrap items-start justify-between gap-3 mb-4">        
    <h2 class="page-title">            
        Laporan dan Ekspor
        </h2>
    </div>

    {{-- Tab Navigation --}}
     @include('admin.laporan._tabs', ['active' => 'riwayat-barang'])

    {{-- Tombol Ekspor (di luar panel filter) --}}
    <div class="flex items-center justify-end gap-2 mb-4">
        <a href="{{ route('admin.laporan.riwayat-barang.export-pdf', request()->query()) }}" target="_blank" class="btn btn-outline">
            Ekspor PDF
        </a>
        <a href="{{ route('admin.laporan.riwayat-barang.export-excel', request()->query()) }}" class="btn btn-outline">
            Ekspor Excel
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.laporan.riwayat-barang.index') }}" class="mb-4 card card-pad">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            <div>
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-control">
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
            <div>
                <label class="form-label">Jenis Transaksi</label>
                <select name="jenis_transaksi" class="form-control">
                    <option value="">-- Semua Jenis --</option>
                    @foreach ($jenisOptions as $jenis)
                        <option value="{{ $jenis }}" {{ request('jenis_transaksi') == $jenis ? 'selected' : '' }}>
                            {{ $jenis }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary">
                    Terapkan
                </button>
                @if (request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'location_id', 'category_id', 'jenis_transaksi']))
                    <a href="{{ route('admin.laporan.riwayat-barang.index') }}" class="text-sm text-gray-500 hover:underline">
                        Reset filter
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Pemohon</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $trx)
                        <tr>
                            <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                            <td class="font-medium text-gray-800">
                                @if ($trx->item)
                                    <a href="{{ route('admin.laporan.riwayat-barang.show', $trx->item) }}" class="text-brand-600 hover:underline">
                                        {{ $trx->item->nama_barang }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>{{ $trx->jenis_transaksi }}</td>
                            <td>
                                <span class="badge badge-slate">
                                    {{ $statusLabels[$trx->status] ?? ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td>{{ $trx->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                Tidak ada data transaksi yang cocok dengan filter.
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