@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Stock In — Barang Baru') }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ __('Satu form ini membuat data barang sekaligus mencatat penerimaan pertama') }}
        </p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action="{{ route('stock-in.store') }}" class="space-y-5">
            @csrf
            @include('admin.items._form', [
                'submitLabel' => __('Simpan dan catat penerimaan'),
                'infoBox' => __('Setelah disimpan — Sistem membuat item_id dan QR otomatis'),
            ])
        </form>
    </div>
@endsection