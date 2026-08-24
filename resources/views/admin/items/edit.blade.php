@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Edit Barang') }}
        </h2>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action="{{ route('barang.update', $item) }}" class="space-y-5">
            @csrf
            @method('put')
            @include('admin.items._form')
        </form>
    </div>
@endsection