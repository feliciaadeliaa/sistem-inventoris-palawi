{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Manajemen Kategori') }}
        </h2>
        <a href="{{ route('kategori.create') }}"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
            + {{ __('Tambah Kategori') }}
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Kode') }}</th>
                    <th class="px-4 py-3">{{ __('Nama Kategori') }}</th>
                    <th class="px-4 py-3">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 text-gray-800 dark:text-white/90">{{ $category->category_id }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $category->nama_kategori }}</td>
                        <td class="px-4 py-3 flex items-center gap-3">
                            <a href="{{ route('kategori.edit', $category) }}" class="text-brand-500 hover:underline">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('kategori.destroy', $category) }}" onsubmit="return confirm('{{ __('Yakin hapus kategori ini?') }}')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="text-error-500 hover:underline">{{ __('Hapus') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('Belum ada data kategori.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection