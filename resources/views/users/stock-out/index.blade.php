@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Riwayat Peminjaman Saya
        </h2>

        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Lihat riwayat pengajuan peminjaman barang Anda.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 flex justify-end">
        <a href="{{ route('peminjaman.create', ['item_id' => 1]) }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            + Ajukan Peminjaman Baru
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Barang
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Keterangan
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Estimasi Kembali
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Status
                    </th>

                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Tanggal Kembali Aktual
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                @forelse ($transactions as $trx)

                    <tr>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $trx->item->nama_barang }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $trx->keterangan }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $trx->tanggal_kembali_estimasi?->format('d M Y') ?? '-' }}
                        </td>

                        <td class="px-4 py-3">

                            <span @class([
                                'px-2 py-1 rounded text-xs font-semibold',

                                'bg-yellow-100 text-yellow-800' =>
                                    $trx->status === 'menunggu_approval',

                                'bg-blue-100 text-blue-800' =>
                                    $trx->status === 'disetujui',

                                'bg-red-100 text-red-800' =>
                                    $trx->status === 'ditolak',

                                'bg-green-100 text-green-800' =>
                                    $trx->status === 'dikembalikan',
                            ])>

                                {{ str_replace('_', ' ', ucfirst($trx->status)) }}

                            </span>

                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $trx->tanggal_kembali_aktual?->format('d M Y') ?? '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">

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