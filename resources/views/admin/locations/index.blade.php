@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
                Manajemen Lokasi
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola data lokasi barang inventaris.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('lokasi.import.form') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                Import Excel/CSV
            </a>
            <a href="{{ route('lokasi.create') }}" class="bg-brand-500 text-white px-4 py-2 rounded hover:bg-brand-600">
                + Tambah Lokasi
            </a>
        </div>
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

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Kode
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Nama Lokasi
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Wilayah
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Unit Bisnis
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Sub Unit Bisnis
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($locations as $location)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $location->kode_lokasi ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $location->nama_lokasi }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $location->wilayah ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $location->unit_bisnis ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                            {{ $location->sub_unit_bisnis ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('lokasi.edit', $location) }}"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    Edit
                                </a>

                                <form action="{{ route('lokasi.destroy', $location) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus lokasi ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            Belum ada data lokasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $locations->links() }}
        </div>
    </div>
@endsection