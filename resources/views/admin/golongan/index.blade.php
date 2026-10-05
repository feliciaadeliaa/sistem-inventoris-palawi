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
                        <th class="sticky-col text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($golongans as $g)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="cell-id">{{ $g->kode }}</td>
                            <td class="font-medium text-gray-800">{{ $g->nama }}</td>
                            <td>{{ $g->masa_manfaat }} {{ __('tahun') }}</td>
                            <td>{{ number_format($g->persen, 2, ',', '.') }}%</td>
                            <td>{{ $g->categories_count }}</td>
                            <td class="sticky-col">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('golongan.edit', $g) }}" title="{{ __('Edit') }}"
                                        class="icon-btn icon-btn-edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('golongan.destroy', $g) }}" method="POST" class="inline-flex"
                                        onsubmit="return confirm('Hapus {{ $g->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="{{ __('Hapus') }}" class="icon-btn icon-btn-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18" />
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                {{ __('Belum ada data golongan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection