@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="page-title">Golongan AT</h2>
            <p class="page-desc">
                Master golongan aktiva tetap. Tiap sub jenis di menu Kategori dihubungkan ke satu golongan, dan masa manfaat aset otomatis mengikutinya.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('golongan.create') }}" class="btn btn-primary">+ Tambah Golongan</a>
        </div>
    </div>

    {{-- Hapus dua blok ini kalau layouts.app sudah menampilkan flash message sendiri --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-secondary-50 text-secondary-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>{{ __('No') }}</th>
                        <th>{{ __('Kode') }}</th>
                        <th>{{ __('Nama Golongan') }}</th>
                        <th>{{ __('Masa Manfaat') }}</th>
                        <th>{{ __('Persen') }}</th>
                        <th>{{ __('Jumlah Sub Jenis') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="sticky-col text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($golongans as $g)
                        <tr class="{{ $g->is_active ? '' : 'opacity-60' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="cell-id">{{ $g->kode }}</td>
                            <td class="font-medium text-gray-800">{{ $g->nama }}</td>
                            <td>{{ $g->masa_manfaat }} {{ __('tahun') }}</td>
                            <td>{{ number_format($g->persen, 2, ',', '.') }}%</td>
                            <td>{{ $g->categories_count }}</td>
                            <td>
                                <span class="badge {{ $g->is_active ? 'badge-green' : 'badge-slate' }}">
                                    {{ $g->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="sticky-col">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form action="{{ route('golongan.toggle-active', $g) }}" method="POST" class="inline-flex"
                                        onsubmit="return confirm('{{ $g->is_active
                                            ? 'Nonaktifkan golongan ini? Golongan tidak akan muncul di pilihan saat membuat kategori baru. Kategori dan aset yang sudah memakainya tidak terpengaruh.'
                                            : 'Aktifkan kembali golongan ini?' }}')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            title="{{ $g->is_active ? __('Nonaktifkan') : __('Aktifkan') }}"
                                            class="icon-btn {{ $g->is_active ? 'icon-btn-danger' : 'icon-btn-outline' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                {{ $search !== '' ? "Tidak ada golongan yang cocok dengan \"{$search}\"." : __('Belum ada data golongan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection