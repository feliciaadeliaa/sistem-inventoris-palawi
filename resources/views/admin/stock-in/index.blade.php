@extends('layouts.app')

@section('content')
    <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Stock In — Konfirmasi Pengembalian Barang</h2>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dipinjam Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal Pinjam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->item->nama_barang }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->user->name }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            @if ($trx->status === 'disetujui')
                                <span class="text-yellow-600 dark:text-yellow-400">Sedang Dipinjam</span>
                            @else
                                <span class="text-green-600 dark:text-green-400">Dikembalikan</span>
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
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
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
@endsection