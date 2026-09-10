@extends('layouts.app')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Manajemen Lokasi') }}
        </h2>
        <div class="flex gap-3">
            <a href="{{ route('lokasi.import.form') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                {{ __('Import Excel/CSV') }}
            </a>
            <a href="{{ route('lokasi.create') }}"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                + {{ __('Tambah Lokasi') }}
            </a>
        </div>
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
                    <th class="px-4 py-3">{{ __('Nama Lokasi') }}</th>
                    <th class="px-4 py-3">{{ __('Wilayah') }}</th>
                    <th class="px-4 py-3">{{ __('Unit Bisnis') }}</th>
                    <th class="px-4 py-3">{{ __('Sub Unit Bisnis') }}</th>
                    <th class="px-4 py-3">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($locations as $location)
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 text-gray-800 dark:text-white/90">{{ $location->kode_lokasi ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-white/90">{{ $location->nama_lokasi }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $location->wilayah ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $location->unit_bisnis ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $location->sub_unit_bisnis ?? '-' }}</td>
                        <td class="px-4 py-3 flex items-center gap-3">
                            <a href="{{ route('lokasi.edit', $location) }}" class="text-brand-500 hover:underline">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('lokasi.destroy', $location) }}" onsubmit="return confirm('{{ __('Yakin hapus lokasi ini?') }}')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="text-error-500 hover:underline">{{ __('Hapus') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('Belum ada data lokasi.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $locations->links() }}
    </div>
@endsection