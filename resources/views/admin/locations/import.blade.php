@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Import Data Lokasi') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ __('Upload file Excel (.xlsx) atau CSV. Kolom: Code, Nama, Wilayah, Unit Bisnis, Sub Unit Bisnis. Data dengan kode yang sudah ada akan DIPERBARUI (bukan dilewati).') }}
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action="{{ route('lokasi.import') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="file" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('File Excel/CSV') }}
                </label>
                <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100 dark:text-gray-400 dark:file:bg-white/5 dark:file:text-brand-400" />
                @error('file')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                    {{ __('Upload & Import') }}
                </button>
                <a href="{{ route('lokasi.index') }}"
                    class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    {{ __('Batal') }}
                </a>
            </div>
        </form>
    </div>
@endsection