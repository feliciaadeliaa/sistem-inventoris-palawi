@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="page-title">
                Manajemen Lokasi
            </h2>
            <p class="page-desc">
                Kelola data klaster dan wisata. Kode klaster + kode lokasi membentuk segmen ke-4 nomor aktiva tetap.
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="form-label">Cari (Kode / Nama)</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode, nama wisata, atau klaster..."
                    class="form-control">
            </div>

            <div>
                <label class="form-label">Klaster</label>
                <select name="unit_bisnis" class="form-control">
                    <option value="">-- Semua --</option>
                    @foreach ($unitBisnisOptions as $unit)
                        <option value="{{ $unit }}" {{ request('unit_bisnis') == $unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">
                Terapkan Filter
            </button>
            @if (request()->anyFilled(['search', 'unit_bisnis']))
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
                        <th>Kode Gabungan</th>
                        <th>Klaster</th>
                        <th>Kode Lokasi</th>
                        <th>Nama Wisata</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($locations as $location)
                        <tr class="{{ $location->is_active ? '' : 'opacity-60' }}">
                            <td class="cell-id">
                                {{ $location->kode_gabungan }}
                            </td>

                            <td>
                                {{ $location->kode_unit_bisnis }} - {{ $location->unit_bisnis }}
                            </td>

                            <td>
                                {{ $location->kode_lokasi }}
                            </td>

                            <td class="font-medium text-gray-800">
                                {{ $location->nama_wisata }}
                            </td>

                            <td>
                                <span class="badge {{ $location->is_active ? 'badge-green' : 'badge-slate' }}">
                                    {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <form action="{{ route('lokasi.toggle-active', $location) }}" method="POST" class="inline"
                                        onsubmit="return confirm('{{ $location->is_active
                                            ? 'Nonaktifkan lokasi ini? Lokasi tidak akan muncul di pilihan saat menambah aset baru. Aset yang sudah ada tidak terpengaruh.'
                                            : 'Aktifkan kembali lokasi ini?' }}')">
                                        @csrf
                                        @method('PATCH')
                                        {{-- Supaya setelah toggle tetap di halaman, pencarian, dan filter yang sedang dibuka --}}
                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                        <input type="hidden" name="q" value="{{ request('q') }}">
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                        <input type="hidden" name="unit_bisnis" value="{{ request('unit_bisnis') }}">

                                        <button type="submit"
                                            title="{{ $location->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            class="icon-btn {{ $location->is_active ? 'icon-btn-danger' : 'icon-btn-outline' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                                                <line x1="12" y1="2" x2="12" y2="12" />
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