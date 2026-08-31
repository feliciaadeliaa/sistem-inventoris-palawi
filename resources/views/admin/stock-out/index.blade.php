@extends('layouts.app')

@section('content')
    <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Antrian Peminjaman (Stock Out)</h2>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pengaju</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estimasi Kembali</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->item->nama_barang }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->user->name }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->keterangan }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->tanggal_kembali_estimasi?->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded text-xs font-semibold',
                                'bg-yellow-100 text-yellow-800' => $trx->status === 'menunggu_approval',
                                'bg-blue-100 text-blue-800' => $trx->status === 'disetujui',
                                'bg-red-100 text-red-800' => $trx->status === 'ditolak',
                                'bg-green-100 text-green-800' => $trx->status === 'dikembalikan',
                            ])>
                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 space-x-1">
                            @if ($trx->status === 'menunggu_approval')
                                <form action="{{ route('admin.transaksi.stock-out.approve', $trx) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="px-2 py-1 bg-green-600 text-white text-xs rounded">Setujui</button>
                                </form>
                                <form action="{{ route('admin.transaksi.stock-out.reject', $trx) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="px-2 py-1 bg-red-600 text-white text-xs rounded">Tolak</button>
                                </form>
                            @elseif ($trx->status === 'disetujui')
                                <span class="text-xs text-gray-500 dark:text-gray-400">Menunggu pengembalian</span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            Belum ada pengajuan peminjaman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection