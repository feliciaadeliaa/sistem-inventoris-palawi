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

    {{-- Filter --}}
    <form method="GET" action="{{ route('lokasi.index') }}" class="mb-4 bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cari (Kode / Nama)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama lokasi..."
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Wilayah</label>
                <select name="wilayah" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach ($wilayahOptions as $wilayah)
                        <option value="{{ $wilayah }}" {{ request('wilayah') == $wilayah ? 'selected' : '' }}>
                            {{ $wilayah }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Unit Bisnis</label>
                <select name="unit_bisnis" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach ($unitBisnisOptions as $unit)
                        <option value="{{ $unit }}" {{ request('unit_bisnis') == $unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sub Unit Bisnis</label>
                <select name="sub_unit_bisnis" class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 dark:bg-gray-900 dark:border-gray-700 dark:text-white/90">
                    <option value="">-- Semua --</option>
                    @foreach ($subUnitBisnisOptions as $sub)
                        <option value="{{ $sub }}" {{ request('sub_unit_bisnis') == $sub ? 'selected' : '' }}>
                            {{ $sub }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded">
                Terapkan Filter
            </button>
            @if (request()->anyFilled(['search', 'wilayah', 'unit_bisnis', 'sub_unit_bisnis']))
                <a href="{{ route('lokasi.index') }}" class="text-sm text-gray-500 hover:underline dark:text-gray-400">
                    Reset filter
                </a>
            @endif
        </div>
    </form>

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
                                    title="Edit"
                                    class="p-2 rounded bg-yellow-500 text-white hover:bg-yellow-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </a>

                                <form action="{{ route('lokasi.destroy', $location) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus lokasi ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        title="Hapus"
                                        class="p-2 rounded bg-red-500 text-white hover:bg-red-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            <line x1="10" y1="11" x2="10" y2="17" />
                                            <line x1="14" y1="11" x2="14" y2="17" />
                                        </svg>
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