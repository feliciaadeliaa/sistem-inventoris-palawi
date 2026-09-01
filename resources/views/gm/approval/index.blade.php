@extends('layouts.app')

@section('content')
    <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Approval — General Manager</h2>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stock Out --}}
    <h3 class="text-lg font-medium mb-3 text-gray-800 dark:text-white">Persetujuan Stock Out (Peminjaman)</h3>
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto mb-8">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Diajukan Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Diproses Admin</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($stockOutPending as $trx)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->item->nama_barang }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->user->name }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->approved_at?->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 space-x-2">
                            <form action="{{ route('gm.approval.stock-out.approve', $trx) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-green-600 text-white px-3 py-1.5 rounded text-sm hover:bg-green-700">Setujui</button>
                            </form>
                            <form action="{{ route('gm.approval.stock-out.reject', $trx) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded text-sm hover:bg-red-700">Tolak</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Tidak ada pengajuan menunggu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mutasi --}}
    <h3 class="text-lg font-medium mb-3 text-gray-800 dark:text-white">Pengesahan Mutasi Lokasi</h3>
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dari</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ke</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Diinput Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($mutasiPending as $trx)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->item->nama_barang }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->lokasiAsal->nama_lokasi }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->lokasiTujuan->nama_lokasi }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->user->name }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('gm.approval.mutasi.acknowledge', $trx) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-700">Sahkan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Tidak ada mutasi menunggu pengesahan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection