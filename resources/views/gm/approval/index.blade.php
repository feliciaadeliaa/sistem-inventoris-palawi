@extends('layouts.app')

@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h2 class="page-title">Approval General Manager</h2>
        <p class="page-desc">Tinjau dan putuskan pengajuan yang sudah diproses admin</p>
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
@if ($errors->any())
    <div class="mb-4 p-4 bg-error-50 text-error-700 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ===================== STOCK OUT ===================== --}}
<div class="card overflow-hidden mb-8">
    <div class="flex items-center justify-between px-4 pt-4 pb-3">
        <h3 class="panel-title mb-0">Persetujuan Stock Out (Peminjaman)</h3>
        <span class="text-sm text-gray-600">
            <span class="font-semibold text-brand-700">{{ $stockOutPending->count() }}</span> menunggu
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Diajukan Oleh</th>
                    <th>Diproses Admin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockOutPending as $trx)
                    @php $dipinjam = $trx->item->status === 'dipinjam'; @endphp
                    <tr>
                        <td class="font-medium text-gray-800">
                            {{ $trx->item->nama_barang }}
                            @if ($dipinjam)
                                <span class="badge badge-amber ml-2">Sudah dipinjam</span>
                            @endif
                        </td>
                        <td>{{ $trx->user->name }}</td>
                        <td>{{ $trx->approved_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('gm.approval.stock-out.approve', $trx) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success"
                                        @disabled($dipinjam)
                                        @if ($dipinjam) title="Barang sudah berstatus dipinjam, tolak pengajuan ini" @endif>
                                        Setujui
                                    </button>
                                </form>
                                <button type="button" class="btn-tolak btn btn-sm btn-danger"
                                    data-action="{{ route('gm.approval.stock-out.reject', $trx) }}"
                                    data-label="{{ $trx->item->nama_barang }} — diajukan {{ $trx->user->name }}">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500">Tidak ada pengajuan menunggu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== MUTASI ===================== --}}
<div class="card overflow-hidden mb-8">
    <div class="flex items-center justify-between px-4 pt-4 pb-3">
        <h3 class="panel-title mb-0">Approval Mutasi Lokasi</h3>
        <span class="text-sm text-gray-600">
            <span class="font-semibold text-brand-700">{{ $mutasiPending->count() }}</span> menunggu
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Lokasi Asal &rarr; Tujuan</th>
                    <th>Diinput Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mutasiPending as $trx)
                    @php $dipinjam = $trx->item->status === 'dipinjam'; @endphp
                    <tr>
                        <td class="font-medium text-gray-800">
                            {{ $trx->item->nama_barang }}
                            @if ($dipinjam)
                                <span class="badge badge-amber ml-2">Sedang dipinjam</span>
                            @endif
                        </td>
                        <td>
                            {{ $trx->lokasiAsal->nama_lokasi ?? '-' }} &rarr; {{ $trx->lokasiTujuan->nama_lokasi ?? '-' }}
                        </td>
                        <td>{{ $trx->user->name }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('gm.approval.mutasi.approve', $trx) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success"
                                        @disabled($dipinjam)
                                        @if ($dipinjam) title="Barang sedang dipinjam, mutasi tidak bisa disetujui. Tolak lalu ajukan ulang setelah dikembalikan." @endif>
                                        Setujui
                                    </button>
                                </form>
                                <button type="button" class="btn-tolak btn btn-sm btn-danger"
                                    data-action="{{ route('gm.approval.mutasi.reject', $trx) }}"
                                    data-label="Mutasi {{ $trx->item->nama_barang }}: {{ $trx->lokasiAsal->nama_lokasi }} → {{ $trx->lokasiTujuan->nama_lokasi }}">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500">Tidak ada mutasi menunggu approval.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== PERBAIKAN ===================== --}}
<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-4 pt-4 pb-3">
        <h3 class="panel-title mb-0">Approval Permintaan Perbaikan</h3>
        <span class="text-sm text-gray-600">
            <span class="font-semibold text-brand-700">{{ $repairPending->count() }}</span> menunggu
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Diajukan Oleh</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($repairPending as $trx)
                    <tr>
                        <td class="font-medium text-gray-800">{{ $trx->item->nama_barang }}</td>
                        <td>{{ $trx->user->name }}</td>
                        <td class="max-w-xs whitespace-normal text-gray-600">{{ $trx->keterangan ?: '-' }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('gm.approval.repair.approve', $trx) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                </form>
                                <button type="button" class="btn-tolak btn btn-sm btn-danger"
                                    data-action="{{ route('gm.approval.repair.reject', $trx) }}"
                                    data-label="Perbaikan {{ $trx->item->nama_barang }} — diajukan {{ $trx->user->name }}">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500">Tidak ada permintaan perbaikan menunggu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('partials.reject-modal')
@endsection