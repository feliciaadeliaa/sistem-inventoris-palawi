@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/10">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 8v4l3 3M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"
                stroke="currentColor" class="text-brand-500" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-2">
        {{ $feature ?? 'Fitur ini' }} — Segera Hadir
    </h2>

    <p class="max-w-md text-sm text-gray-500 dark:text-gray-400">
        Fitur ini masih menunggu kejelasan alur/kebijakan dari kantor. Pengembangan sementara dihentikan dan akan dilanjutkan setelah ada keputusan lebih lanjut.
    </p>

    <a href="{{ route('dashboard') }}"
        class="mt-6 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
        Kembali ke Dashboard
    </a>
</div>
@endsection