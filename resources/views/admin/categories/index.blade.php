@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h2 class="page-title">
                Manajemen Kategori
            </h2>
            <p class="page-desc">
                Kelola Jenis Aktiva Tetap dan Sub Jenis yang dipakai untuk penomoran aset.
            </p>
        </div>

        <a href="{{ route('kategori.create') }}" class="btn btn-primary">
            + Tambah Kategori
        </a>
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

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>Kode Aktiva Tetap</th>
                        <th>Jenis Aktiva Tetap</th>
                        <th>Sub Jenis</th>
                        <th>Keterangan Fungsi</th>
                        <th>Golongan AT</th>
                        <th>Masa Manfaat</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categories as $category)
                        <tr class="{{ $category->is_active ? '' : 'opacity-60' }}">
                            <td class="cell-id">
                                {{ $category->kode_aktiva_tetap }}
                            </td>

                            <td class="font-medium text-gray-800">
                                {{ $category->jenis_aktiva_tetap }}
                            </td>

                            <td class="cell-id">
                                {{ $category->sub_jenis ?? '-' }}
                            </td>

                            <td class="text-gray-700">
                                {{ $category->keterangan_fungsi ?? '-' }}
                            </td>

                            <td>
                                @if ($category->golongan)
                                    <span class="badge badge-slate">{{ $category->golongan->nama }}</span>
                                @elseif ($category->sub_jenis)
                                    <span class="badge badge-red" title="Atur golongan lewat tombol edit">Belum diatur</span>
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $category->golongan ? $category->golongan->masa_manfaat . ' ' . __('tahun') : '-' }}
                            </td>

                            <td>
                                <span class="badge {{ $category->is_active ? 'badge-green' : 'badge-slate' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('kategori.edit', $category) }}"
                                        title="Edit"
                                        class="icon-btn icon-btn-edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('kategori.toggle-active', $category) }}" method="POST" class="inline"
                                        onsubmit="return confirm('{{ $category->is_active
                                            ? 'Nonaktifkan kategori ini? Kategori tidak akan muncul di pilihan saat menambah aset baru. Aset yang sudah ada tidak terpengaruh.'
                                            : 'Aktifkan kembali kategori ini?' }}')">
                                        @csrf
                                        @method('PATCH')
                                        {{-- Supaya setelah toggle tetap di halaman & pencarian yang sedang dibuka --}}
                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                        <input type="hidden" name="q" value="{{ request('q') }}">

                                        <button type="submit"
                                            title="{{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            class="icon-btn {{ $category->is_active ? 'icon-btn-danger' : 'icon-btn-outline' }}">
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
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-pad !pt-4">
            {{ $categories->links() }}
        </div>
    </div>
@endsection