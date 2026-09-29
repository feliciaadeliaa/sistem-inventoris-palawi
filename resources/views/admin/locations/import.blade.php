@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="page-title">
            {{ __('Import Data Lokasi') }}
        </h2>
        <p class="page-desc">
            {{ __('Upload file Excel (.xlsx) atau CSV. Data dengan kode lokasi yang sudah ada akan DIPERBARUI (bukan dilewati).') }}
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Petunjuk format --}}
    <div class="mb-4 card card-pad">
        <p class="text-sm font-medium text-gray-800 mb-2">{{ __('Format kolom (baris pertama = header):') }}</p>

        <div class="overflow-x-auto">
            <table class="table-app">
                <thead>
                    <tr>
                        <th>kode_unit_bisnis</th>
                        <th>unit_bisnis</th>
                        <th>kode_lokasi</th>
                        <th>nama_wisata</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>KLASTER MALANG</td>
                        <td>01</td>
                        <td>COBAN RONDO</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>KLASTER MALANG</td>
                        <td>02</td>
                        <td>COBAN RAIS</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ul class="mt-3 list-disc list-inside text-sm text-gray-500 space-y-1">
            <li>{{ __('kode_unit_bisnis (kode klaster) 1 digit, kode_lokasi 2 digit. Angka nol di depan otomatis dilengkapi (1 menjadi 01 untuk kode lokasi).') }}</li>
            <li>{{ __('Satu kode klaster harus selalu punya nama klaster yang sama.') }}</li>
            <li>{{ __('Lokasi yang sudah dipakai barang tidak bisa dipindah ke klaster lain.') }}</li>
        </ul>
    </div>

    <div class="card card-pad">
        <form method="POST" action="{{ route('lokasi.import') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="file" class="form-label">
                    {{ __('File Excel/CSV') }}
                </label>
                <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100" />
                @error('file')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary">
                    {{ __('Upload & Import') }}
                </button>
                <a href="{{ route('lokasi.index') }}" class="btn btn-outline">
                    {{ __('Batal') }}
                </a>
            </div>
        </form>
    </div>
@endsection