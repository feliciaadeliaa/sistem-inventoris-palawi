@extends('layouts.app')

@section('content')
    <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Riwayat Stock In</h2>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Diinput Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->item->nama_barang }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->user->name }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $trx->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection