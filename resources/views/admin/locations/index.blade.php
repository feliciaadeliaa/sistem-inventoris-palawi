@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="page-title">
                Manajemen Lokasi
            </h2>
            <p class="page-desc">
                Kelola data lokasi barang inventaris.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('lokasi.import.form') }}" class="btn btn-outline">
                Import Excel/CSV
            </a>
            <a href="{{ route('lokasi.create') }}" class="btn btn-primary">
                + Tambah Lokasi
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-secondary-50 text-secondary-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-error-50 text-error-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('lokasi.index') }}" class="mb-4 card card-pad">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="form-label">Cari (Kode / Nama)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama lokasi..."
                    class="form-control">
            </div>

            <div>
                <label class="form-label">Wilayah</label>
                <select name="wilayah" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach ($wilayahOptions as $wilayah)
                        <option value="{{ $wilayah }}" {{ request('wilayah') == $wilayah ? 'selected' : '' }}>
                            {{ $wilayah }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Unit Bisnis</label>
                <select name="unit_bisnis" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach ($unitBisnisOptions as $unit)
                        <option value="{{ $unit }}" {{ request('unit_bisnis') == $unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Sub Unit Bisnis</label>
                <select name="sub_unit_bisnis" class="form-control">
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
            <button type="submit" class="btn btn-primary">
                Terapkan Filter
            </button>
            @if (request()->anyFilled(['search', 'wilayah', 'unit_bisnis', 'sub_unit_bisnis']))
                <a href="{{ route('lokasi.index') }}" class="text-sm text-gray-500 hover:underline">
                    Reset filter
                </a>
            @endif
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Lokasi</th>
                        <th>Wilayah</th>
                        <th>Unit Bisnis</th>
                        <th>Sub Unit Bisnis</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($locations as $location)
                        <tr>
                            <td class="cell-id">
                                {{ $location->kode_lokasi ?? '-' }}
                            </td>

                            <td class="font-medium text-gray-800">
                                {{ $location->nama_lokasi }}
                            </td>

                            <td>
                                {{ $location->wilayah ?? '-' }}
                            </td>

                            <td>
                                {{ $location->unit_bisnis ?? '-' }}
                            </td>

                            <td>
                                {{ $location->sub_unit_bisnis ?? '-' }}
                            </td>

                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('lokasi.edit', $location) }}"
                                        title="Edit"
                                        class="icon-btn icon-btn-edit">
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
                                            class="icon-btn icon-btn-danger">
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
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data lokasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-pad !pt-4">
            {{ $locations->links() }}
        </div>
    </div>
@endsection